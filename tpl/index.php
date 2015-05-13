<!DOCTYPE html>
<html>
<head>
<meta charter="utf-8" />
<title>dokufarmer</title>
</head>
<body>
<a href="add.php">Add new wiki</a>
<table>
<tr>
	<th>Informations</th>
	<th>Plugins</th>
</tr>
<?php foreach(get_wikis() as $wiki): ?>

<?php endforeach ?>
</table>
</body> 
</html>
