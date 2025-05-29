<?php section('content')?>
   
<section>
  <h1>Bienvenido a VlexLand 🚀</h1>
  <p>Tu motor de plantillas hecho en casa con amor y velocidad.</p>
  <a href="#funciones" role="button">Descubre más</a>
</section>

<section id="funciones">
  <h2>Hola <?php echo $name?></h2>
  <ul>
    <li>Sintaxis sencilla</li>
    
    <li>Zero dependencies</li>
    <li> Hecho por ti 😎</li>
  </ul>
</section>

<section>
  <h2>Comienza ahora</h2>
  <form method="post">
    <label>
      Tu email:
      <input type="email" name="email" placeholder="tucorreo@ejemplo.com" required>
    </label>
   
      <button type="submit">Quiero saber más</button>
   
  </form>
</section>

<?php endsection()?>