<h3 style="word-break: break-all; overflow: hidden; text-overflow: ellipsis;">Hallo <?php echo $to; ?></h3>
<p style="text-align: justify;">Ini adalah pesan yang anda tulis di menu <strong>web setting</strong> system <?php echo setting('title'); ?>. Jika anda menerima email ini berarti konfigurasi SMTP email anda sudah benar. Adapun pesan yang anda tulis di pesan tersebut adalah sebagai berikut:</p>
<div style="color: #e83e8c; padding: 15px 0; text-align: center; font-family: SFMono-Regular,Menlo,Monaco,Consolas,'Liberation Mono','Courier New',monospace;">
	<?php echo $message; ?>
</div>