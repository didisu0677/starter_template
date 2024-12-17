<div class="table-responsive">
    <table class="table table-bordered table-app table-detail table-normal">
        <?php if(isset($attr)) { ?>
        <?php foreach($detail as $k => $d) { ?>
        <?php if(isset($attr['label'][$k]) && $k != 'id') { ?>
        <tr>
            <th><?php echo lang(strtolower(str_replace(' ','_',$attr['label'][$k])),$attr['label'][$k]); ?></th>
            <td><?php echo $d; ?></td>
        </tr>
        <?php } ?>
        <?php } ?>
        <?php } else { ?>
        <?php foreach($detail as $k => $d) { ?>
        <tr>
            <th><?php echo $k; ?></th>
            <td><?php echo $d; ?></td>
        </tr>
        <?php } ?>
        <?php } ?>
    </table>
</div>