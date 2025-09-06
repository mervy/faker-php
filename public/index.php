<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// use the factory to create a Faker\Generator instance
$faker = Factory::create();
// generate data by calling methods
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">';
echo "<h1 class='text-center p-5'>Fake Datas for testing</h1>
<div class=\"container\">    
    <table class=\"table table-striped\">
        <tr>      
            <th scope=\"co\">Id</th>
            <th scope=\"co\">UUID</th>
            <th scope=\"col\">Name</th>
            <th scope=\"col\">Password</th>
            <th scope=\"col\">Password Hash</th>
        </tr>";

// Método 1: Truncar a senha gerada

for ($i=0; $i < 10000; $i++) { 
//Dentro do loop para gerar senhas diferentes
    $password = $faker->password(12);
$truncatedPassword = substr($password, 0, 12);
    echo "    
        <tr>
           <td>". $i + 1 ."</td>
            <td>". $faker->uuid() ."</td>
            <td>". $faker->name() ."</td>
            <td>". $truncatedPassword ."</td>
            <td>". password_hash($truncatedPassword, PASSWORD_DEFAULT) ."</td>
        </tr>";
}
 echo "
    </table>   
    </div>
</div> 
    ";

// echo '<pre>'. $faker->name();
// // 'Vince Sporer'
// echo '<pre>'. $faker->email();
// // 'walter.sophia@hotmail.com'
// echo'<pre>'. $faker->text();
// // 'Numquam ut mollitia at consequuntur inventore dolorem.'