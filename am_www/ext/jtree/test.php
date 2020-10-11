<html dir="rtl">
<head>
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="jqueryFileTree.js"></script>
<link rel="stylesheet" type="text/css" href="jqueryFileTree.css">
</head>
<body>
<div id="c_id" style="width:180px;"></div>
<script type="text/javascript">
$(document).ready( function() {
    $('#c_id').fileTree({ root: '/' }, function(file) {
        alert(file);
    });
});
</script>
</body>
</html>
