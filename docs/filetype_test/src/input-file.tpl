<!doctype html>
<html><head>

<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>input type=file Test</title>
@@<main.css>@@

</head><body>
<main>
	<input id='pfile' type='file' multiple>
</main>

<ol id='plist' class='block'>
</ol>

<footer>

<script>
	var PLIST = document.getElementById('plist');
</script>

@@<input-file.js>@@

</footer>
</body></html>
