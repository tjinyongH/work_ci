<!-- login.php -->
<form method="post" action="login/authenticate">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit">Login</button>
</form>
<?php if(session()->getFlashdata('error')): ?>
    <p><?= session()->getFlashdata('error'); ?></p>
<?php endif; ?>