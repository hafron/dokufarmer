<!DOCTYPE html>
<html>
<head>
<meta charter="utf-8" />
<title>Dodawanie nowego wiki - dokufarmer</title>
</head>
<body>
<form action="save.php" method="POST">
<fieldset>
<legend>FTP</legend>
<label>Host: <input type="text" name="host" /></label>
<label>Dir: <input type="text" name="dir" value="/" /></label>
<label>User: <input type="text" name="user" /></label>
<label>Password: <input type="password" name="password" /></label>
<label>Use SSL: <input type="checkbox" name="usessl" checked="checked" /></label>
</fieldset>

<fieldset>
<legend>Plugins</legend>
<?php foreach (get_plugins() as $p): ?>
	<?php echo $p[0]['name'] ?>[<?php echo $p[0]['date'] ?>] <input type="checkbox" name="plugins[<?php echo $p[0]['name'] ?>]" /><br />
<?php endforeach ?>
</fieldset>
<input type="submit" value="Zapisz" />
</form>
</body>
</html>
