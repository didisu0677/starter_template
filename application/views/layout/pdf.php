<!DOCTYPE html>
<html>
<head>
    <title>PDF</title>
    <link rel="stylesheet" href="assets/css/template-pdf.css" />
    <style type="text/css">
    body {font-size: <?php echo isset($font_size) ? $font_size : '11'; ?>px; line-height: <?php echo isset($line_height) ? $line_height : '1.5'; ?>}
    table {border-collapse: collapse;}
    td {vertical-align: top;}
    p {text-align: justify;}
    .text-bold {font-weight: bold;}
    .panel-heading {text-transform: uppercase;font-weight: bold;}
    .text-center {text-align: center;}
    .text-right {text-align: right;}
    .panel-body {padding: 10px;}
    .watermark {position: fixed;top: 30%;width: 100%;text-align: center;opacity: .2;transform: rotate(45deg);transform-origin: 50% 50%;z-index: -1000; font-size: 100px}
    table[border] th, table[border] td {
        padding: 2px 4px;
        border-color: #484848;
    }
    thead th, tfoot th {background-color: #eee;}
    @page {margin-bottom: .75in;}
    .break-page, .new-page { page-break-before: always; }
    </style>
</head>
 
<body>
<?php echo $view_content; ?>
</body>
</html>