@section('content')
      <h1>Motor de Plantillas Vlex</h1>
  <p>Este motor de plantillas permite escribir código HTML mezclado con directivas al estilo de Blade (Laravel), que son compiladas a PHP.</p>

  <h2>Características Soportadas</h2>
  <ul>
    <li><strong>Impresión con escape:</strong> <code>{{ $variable }}</code></li>
    <li><strong>Impresión sin escape:</strong> <code>{!! $variable !!}</code></li>
    <li><strong>Comentarios:</strong> <code>{{-- Esto es un comentario --}}</code></li>
    <li><strong>Condicionales:</strong>
      <ul>
        <li><code>@if (condición)</code></li>
        <li><code>@elseif (condición)</code></li>
        <li><code>@else</code></li>
        <li><code>@endif</code></li>
      </ul>
    </li>
    <li><strong>Bucles:</strong>
      <ul>
        <li><code>@foreach ($items as $item)</code> ... <code>@endforeach</code></li>
        <li><code>@for ($i = 0; $i &lt; 10; $i++)</code> ... <code>@endfor</code></li>
      </ul>
    </li>
    <li><strong>Verificación de variables:</strong>
      <ul>
        <li><code>@isset($variable)</code> ... <code>@endisset</code></li>
        <li><code>@empty($variable)</code> ... <code>@endempty</code></li>
      </ul>
    </li>
    <li><strong>Inclusión de vistas:</strong> <br>
      <code>{{ include('ruta/archivo', ['clave' =&gt; 'valor']) }}</code>
    </li>
    <li><strong>Componentes:</strong>
      <ul>
        <li><code>@component('nombre', ['foo' =&gt; 'bar'])</code></li>
        <li>Contenido interno (slot)</li>
        <li><code>@endcomponent</code></li>
      </ul>
    </li>
    <li><strong>Secciones:</strong>
      <ul>
      <h1>Motor de Plantillas Vlex</h1>
  <p>Este motor de plantillas permite escribir código HTML mezclado con directivas al estilo de Blade (Laravel), que son compiladas a PHP.</p>

  <h2>Características Soportadas</h2>
  <ul>
    <li><strong>Impresión con escape:</strong> <code>{{ $variable }}</code></li>
    <li><strong>Impresión sin escape:</strong> <code>{!! $variable !!}</code></li>
    <li><strong>Comentarios:</strong> <code>{{-- Esto es un comentario --}}</code></li>
    <li><strong>Condicionales:</strong>
      <ul>
        <li><code>@if (condición)</code></li>
        <li><code>@elseif (condición)</code></li>
        <li><code>@else</code></li>
        <li><code>@endif</code></li>
      </ul>
    </li>
    <li><strong>Bucles:</strong>
      <ul>
        <li><code>@foreach ($items as $item)</code> ... <code>@endforeach</code></li>
        <li><code>@for ($i = 0; $i &lt; 10; $i++)</code> ... <code>@endfor</code></li>
      </ul>
    </li>
    <li><strong>Verificación de variables:</strong>
      <ul>
        <li><code>@isset($variable)</code> ... <code>@endisset</code></li>
        <li><code>@empty($variable)</code> ... <code>@endempty</code></li>
      </ul>
    </li>
    <li><strong>Inclusión de vistas:</strong> <br>
      <code>{{ include('ruta/archivo', ['clave' =&gt; 'valor']) }}</code>
    </li>
    <li><strong>Componentes:</strong>
      <ul>
        <li><code>@component('nombre', ['foo' =&gt; 'bar'])</code></li>
        <li>Contenido interno (slot)</li>
        <li><code>@endcomponent</code></li>
      </ul>
    </li>
    <li><strong>Secciones:</strong>
      <ul>
      <h1>Motor de Plantillas Vlex</h1>
  <p>Este motor de plantillas permite escribir código HTML mezclado con directivas al estilo de Blade (Laravel), que son compiladas a PHP.</p>

  <h2>Características Soportadas</h2>
  <ul>
    <li><strong>Impresión con escape:</strong> <code>{{ $variable }}</code></li>
    <li><strong>Impresión sin escape:</strong> <code>{!! $variable !!}</code></li>
    <li><strong>Comentarios:</strong> <code>{{-- Esto es un comentario --}}</code></li>
    <li><strong>Condicionales:</strong>
      <ul>
        <li><code>@if (condición)</code></li>
        <li><code>@elseif (condición)</code></li>
        <li><code>@else</code></li>
        <li><code>@endif</code></li>
      </ul>
    </li>
    <li><strong>Bucles:</strong>
      <ul>
        <li><code>@foreach ($items as $item)</code> ... <code>@endforeach</code></li>
        <li><code>@for ($i = 0; $i &lt; 10; $i++)</code> ... <code>@endfor</code></li>
      </ul>
    </li>
    <li><strong>Verificación de variables:</strong>
      <ul>
        <li><code>@isset($variable)</code> ... <code>@endisset</code></li>
        <li><code>@empty($variable)</code> ... <code>@endempty</code></li>
      </ul>
    </li>
    <li><strong>Inclusión de vistas:</strong> <br>
      <code>{{ include('ruta/archivo', ['clave' =&gt; 'valor']) }}</code>
    </li>
    <li><strong>Componentes:</strong>
      <ul>
        <li><code>@component('nombre', ['foo' =&gt; 'bar'])</code></li>
        <li>Contenido interno (slot)</li>
        <li><code>@endcomponent</code></li>
      </ul>
    </li>
    <li><strong>Secciones:</strong>
      <ul>
        {{-- <li><code>@section('nombre')</code> ... <code>@endsection</code></li> --}}
        <li><code>@yield('nombre')</code></li>
      </ul>
    </li>
    <li><strong>Manejo de errores en expresiones:</strong> Las expresiones dentro de <code>{{ ... }}</code> muestran errores inline si hay fallos (como variable indefinida).</li>
  </ul>

  <h2>Compatibilidad</h2>
  <p>Este HTML es completamente compatible con PicoCSS para un diseño limpio y responsive sin estilos personalizados adicionales.</p>

@endsection