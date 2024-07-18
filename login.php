
<style>

    form{
        margin: 20vh auto;
        max-width: 400px;
        padding: 15px;
        border: 1px solid gray;
        border-radius: 10px;
    }   

</style>
<?php
    
    require 'components/menu.php';

    echo "
            <form action='process_login.php' method='post'>
    <div class='mb-3'>
        <label for='InputId' class='form-label'>Id no Jogo</label>
        <input type='number' class='form-control' id='InputId' name='iduser' aria-describedby='InputId'>
    </div>
    <div class='mb-3'>
        <label for='exampleInputPassword1' class='form-label'>Password</label>
        <input type='password' class='form-control' id='exampleInputPassword1' name='password'>
    </div>
    <button type='submit' class='btn btn-secondary'>Entrar</button>
    <button type='submit' class='btn btn-secondary'>Cadastre-se</button>
</form>"
;

?>