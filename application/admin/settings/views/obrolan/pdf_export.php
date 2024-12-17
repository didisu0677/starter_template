<img src="<?php echo dir_upload('setting').setting('logo'); ?>" style="display: block; margin-bottom: 10px; width: 150px;" />
<h3 style="font-size: 14px;">Transkrip Obrolan</h3>
<div style="margin-bottom: 5px"></div>
<table style="margin-bottom: 20px;">
    <tr>
        <td width="100">Nama Grup</td>
        <td width="10" style="text-align: center;">:</td>
        <td><?php echo $info['nama']; ?></td>
    </tr>
    <tr>
        <td>Anggota Grup</td>
        <td style="text-align: center;">:</td>
        <td><?php echo $info['anggota']; ?></td>
    </tr>
    <?php if($periode != 'Sepanjang Waktu') { ?>
    <tr>
        <td>Periode</td>
        <td style="text-align: center;">:</td>
        <td><?php echo $periode; ?></td>
    </tr>
    <?php } ?>
</table>
<table border="1" width="100%">
    <thead>
        <tr>
            <th width="90">Tanggal / Waktu</th>
            <th width="160">Pengirim</th>
            <th>Pesan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $d) { ?>
        <tr>
            <td><?php echo c_date($d['tanggal']); ?></td>
            <td><?php echo $d['nama']; ?></td>
            <td><?php echo $d['pesan']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>