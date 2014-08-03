<div class="container">
	<a href="<?php echo base_url('download'); ?>" class="btn btn-success pull-right">Download PDF</a>
	<table width="100%" class="table-bordered">
		<thead>
		<tr>
			<td align="center"><strong>รหัสนักเรียน</strong></td>
			<td align="center"><strong>ชื่อ - สกุล</strong></td>
			<td align="center"></td>
		</tr>
		</thead>
		<?php foreach ($query as $row): ?>
		<tr>
			<td width="30%" align="center">
				<?php echo $row->id_student; ?>
			</td>
			<td width="20%" align="center">
				<?php echo $row->name_student; ?> - <?php echo $row->surename_student; ?>
			</td>
			<td width="30%" align="center">
				<a href="<?php echo base_url('student/'.$row->id_student.'/pdf'); ?>">ดู pdf</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p><?php echo $links; ?></p>
</div>