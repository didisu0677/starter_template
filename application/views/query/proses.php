<?php if(isset($error)) { ?>
<div class="alert alert-warning mb-2 alert-dismissible fade show" role="alert">
    Hanya berlaku untuk query <strong>SELECT</strong> saja.
</div>
<?php } else { ?>
<div class="alert alert-secondary mb-2 alert-dismissible fade show" role="alert">
    <code class="font-weight-bold"><?php echo $query; ?></code>
</div>
<?php if(count($record) > 0) { ?>
<div class="table-responsive">
    <table class="table table-bordered table-app">
        <thead>
            <tr>
                <?php foreach($record[0] as $k => $v) { ?>
                <th><?php echo $k; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($record as $r) { ?>
            <tr>
                <?php foreach($record[0] as $k => $v) { ?>
                <td><?php echo $r[$k]; ?></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    Tidak ada data.
</div>
<?php } ?>
<?php } ?>