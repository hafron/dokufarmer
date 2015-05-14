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
<?php foreach(wikidb_get() as $wiki): ?>
<tr>
	<td>
		<?php echo $wiki['host'] ?>
	</td>
	<td>
		<?php foreach (get_plugins_ftp(wftp_connect($wiki)) as $p): ?>
			<?php echo $p['name'] ?>[<?php echo $p['date'] ?>]<br>
		<?php endforeach ?>
	</td>
<tr>
<?php endforeach ?>
</table>
</body> 
</html>
