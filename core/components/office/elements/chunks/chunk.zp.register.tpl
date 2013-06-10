<form action="" method="post" class="form-horizontal" id="office-zpayment-form-create" [[+mode:is=`activate`:then=`style="display:none;"`:else=``]]>
	<input type="hidden" name="action" value="ZPayment/createPurse" />
	<div class="header">
		<small>[[%office_zp_create_header]]</small>
	</div>

	<div class="control-group">
		<label class="control-label">[[%office_zp_sname]]<sup class="red">*</sup></label>
		<div class="controls">
			<input type="text" name="s_name" value="[[+s_name]]" placeholder="[[%office_zp_sname]]" maxlength="50" />
			<span class="help-inline message"></span>
			<br/><small>[[%office_zp_sname_desc]]</small>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">[[%office_zp_fname]]<sup class="red">*</sup></label>
		<div class="controls">
			<input type="text" name="f_name" value="[[+f_name]]" placeholder="[[%office_zp_fname]]" maxlength="50" />
			<span class="help-inline message"></span>
			<br/><small>[[%office_zp_fname_desc]]</small>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">[[%office_zp_mname]]<sup class="red">*</sup></label>
		<div class="controls">
			<input type="text" name="m_name" value="[[+m_name]]" placeholder="[[%office_zp_mname]]" maxlength="50" />
			<span class="help-inline message"></span>
			<br/><small>[[%office_zp_mname_desc]]</small>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">[[%office_zp_phone]]<sup class="red">*</sup></label>
		<div class="controls">
			<input type="text" name="phone" value="[[+phone]]" placeholder="[[%office_zp_phone]]" maxlength="11" />
			<span class="help-inline message"></span>
			<br/><small>[[%office_zp_phone_desc]]</small>
		</div>
	</div>


	<div class="form-actions">
		<button type="submit" class="btn btn-primary">[[%office_zp_submit]]</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<button type="reset" class="btn">[[%office_zp_reset]]</button>
	</div>
</form>


<form action="" method="post" class="form-horizontal" id="office-zpayment-form-activate" [[+mode:is=`new`:then=`style="display:none;"`:else=``]]>
	<input type="hidden" name="action" value="ZPayment/activatePurse" />
	<div class="header">
		<small>[[%office_zp_activate_header]]</small>
	</div>

	<div class="control-group">
		<label class="control-label">[[%office_zp_code]]<sup class="red">*</sup></label>
		<div class="controls">
			<input type="text" name="code" value="" placeholder="[[%office_zp_code]]" maxlength="4" />
			<span class="help-inline message"></span>
			<br/><small>[[%office_zp_code_desc]]</small>
		</div>
	</div>
	<div class="footer">
		<small>[[%office_zp_activate_footer]]</small>
	</div>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">[[%office_zp_submit]]</button>
	</div>
</form>