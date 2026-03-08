# Agents and Skills

Valeria includes an optional layer for **skills** (reusable capabilities) and **agents** (orchestrators that run skills or chat with an LLM). No API key is required to use skills; the LLM is optional.

## Concepts

- **Skill** — A single capability with a name, description, parameters, and `execute()`. Lives in `app/Skills/` and implements `Core\Contracts\SkillInterface`.
- **SkillRegistry** — Discovers and registers all skills in `app/Skills/`. Use it to run a skill by name or list available skills.
- **Agent profile** — A named agent with its own system prompt and optional skill filter. Lives in `app/Agents/` and implements `Core\Contracts\AgentProfileInterface` (or extends `Core\AgentProfile`).
- **Agent** — Orchestrator that runs skills or chats with the LLM; when chatting, it can use a specific agent profile (name, prompt, allowed skills).

## Creating an agent

**CLI:**

```bash
php lucid make:agent Support
```

This creates `app/Agents/SupportAgent.php` with `name()`, `systemPrompt()`, and `allowedSkills()`. Customize the system prompt and optionally restrict which skills this agent can use.

**Manual example:**

```php
<?php

namespace App\Agents;

use Core\AgentProfile;

class SalesAgent extends AgentProfile
{
    public static function name(): string
    {
        return 'sales';
    }

    public static function systemPrompt(): string
    {
        return 'You are a sales assistant. Be friendly and suggest products when relevant.';
    }

    public static function allowedSkills(): ?array
    {
        return ['echo']; // only these skills; null = all
    }
}
```

Agents are discovered automatically from `app/Agents/`; no registration step is needed.

## Creating a skill

**CLI:**

```bash
php lucid make:skill Summarize
```

This creates `app/Skills/SummarizeSkill.php`. Implement `name()`, `description()`, `parameters()`, and `execute()`.

**Manual example:**

```php
<?php

namespace App\Skills;

use Core\Contracts\SkillInterface;

class EchoSkill implements SkillInterface
{
    public static function name(): string { return 'echo'; }
    public static function description(): string { return 'Echoes the given message.'; }
    public static function parameters(): array { return ['message']; }

    public function execute(array $input): array
    {
        $message = $input['message'] ?? '';
        return ['success' => true, 'result' => ['echo' => $message]];
    }
}
```

Skills are discovered automatically when the app or CLI runs; no registration step is needed.

## SkillInterface

| Method | Return | Description |
|--------|--------|-------------|
| `name()` | string | Unique identifier to invoke the skill (e.g. `echo`). |
| `description()` | string | Short description for the agent or API. |
| `parameters()` | array of strings | Parameter names (e.g. `['message', 'lang']`). |
| `execute($input)` | array | Must return `['success' => bool, 'result' => ...]` or `['success' => false, 'error' => '...']`. |

## CLI

From the project root:

| Command | Description |
|---------|-------------|
| `php lucid skill:list` | List all registered skills. |
| `php lucid make:skill <Name>` | Create a new skill in `app/Skills/`. |
| `php lucid agent:list` | List all registered agent profiles. |
| `php lucid make:agent <Name>` | Create a new agent in `app/Agents/`. |
| `php lucid agent:run --skill=echo --input='{"message":"hello"}'` | Run a skill with JSON input. |
| `php lucid agent:run --message="Your question" [--agent=default]` | Chat with the LLM; use `--agent=<name>` to use a specific agent profile. |

## HTTP API

- **GET /api/ai/config** — Returns AI config (default provider, temperature, providers with model and base_url; no API keys).
- **GET /api/skills** — Returns a JSON list of skills (name, description, parameters).
- **GET /api/agents** — Returns a JSON list of agent profiles (name, systemPrompt, allowedSkills).
- **POST /api/agent** — Run a skill or chat (optional body field `agent` to select a profile).

**Run a skill:**

```json
POST /api/agent
Content-Type: application/json

{
  "skill": "echo",
  "input": { "message": "hello" }
}
```

**Chat with the LLM (optionally with an agent profile):**

```json
POST /api/agent
Content-Type: application/json

{
  "message": "What can you do?",
  "agent": "default"
}
```

Omit `agent` to use the default system prompt; set `agent` to a registered name (e.g. `default`, `support`) to use that profile’s prompt and allowed skills. If `OPENAI_API_KEY` is not set, the chat response will indicate that and list available skills.

## LLM and AI config

Models and providers are configured in **`config/ai.php`** (not in `.env`). There you can:

- Set the **default provider** (e.g. `openai`, `openai_gpt4`).
- Set **temperature** (default 0.7).
- Define **providers** with `model`, `base_url`, and `env_key` (the .env variable for the API key).

Only the API key goes in `.env` (e.g. `OPENAI_API_KEY`). Changing model or adding a provider is done by editing `config/ai.php`. See [Configuration](configuration.md#ai--agent-optional).

**GET /api/ai/config** returns the current AI config (default, temperature, gateway status, providers; no secrets).

**AI Gateway** — In `config/ai.php` you can enable the gateway (`gateway.enabled => true`) so that all LLM calls are routed by model, with optional fallback, cache, and rate limit. See [Configuration – AI Gateway](configuration.md#ai-gateway).

The agent injects the list of available skills into the system prompt so the model can suggest using them.

## Prompts

Templates live in `config/prompts.php`. Use the `agent` key and placeholders like `{{ message }}`. Load in code:

```php
use Core\AI\Prompt;

$text = Prompt::load('agent.system', ['context' => '...']);
```

## Using the Agent in code

```php
use Core\Agent;

$agent = new Agent();

// Run a skill
$result = $agent->runSkill('echo', ['message' => 'hi']);

// Chat with default prompt (needs OPENAI_API_KEY)
$result = $agent->chat('What is 2+2?');

// Chat with a specific agent profile (app/Agents/)
$result = $agent->chatWithAgent('support', 'Help me choose a plan');

// List skills and agents
$skills = $agent->listSkills();
$agents = $agent->listAgents();
```

## Custom LLM

Inject a different provider:

```php
use Core\Agent;
use Core\AI\LLMInterface;

class MyLLM implements LLMInterface {
    public function chat(array $messages, array $options = []): string { /* ... */ }
    public function isAvailable(): bool { return true; }
}

$agent = new Agent(new MyLLM());
```
