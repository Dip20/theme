<?= $this->extend(THEME . 'Auth/base') ?>

<?= $this->section('content') ?>
<!-- Page -->
<div class="page main-signin-wrapper">

<!-- Row -->
<div class="row text-center pl-0 pr-0 ml-0 mr-0">
	<div class="col-lg-3 d-block mx-auto">
		<div class="text-center mb-2">
			<img src="<?= ASSETS;?>img/brand/logo.png" class="header-brand-img" alt="logo">
			<img src="<?= ASSETS;?>img/brand/logo-light.png" class="header-brand-img theme-logos" alt="logo">
		</div>
		<div class="card custom-card">
			<div class="card-body">
				<h4 class="text-center">Signup to Your Account</h4>
                <?= $this->include(THEME . 'component/flashmsg') ?>


				<?php $validation =  \Config\Services::validation();?>
				<!-- Display errors -->


				<?php  echo form_open('UserAuth/register');?>
					<div class="form-group text-left">
						<label>Name</label>
						<input class="form-control" placeholder="Enter your Name" type="text" name="name">
						<p class="text-danger mt-3"><?php if ($validation->hasError('name')) { echo $validation->getError('name');}?></p>
					</div>
					<div class="form-group text-left">
						<label>Email</label>
						<input class="form-control" placeholder="Enter your email" type="email" name="email">
						<p class="text-danger mt-3"><?php if ($validation->hasError('email')) { echo $validation->getError('email');}?></p>

					</div>
					<div class="form-group text-left">
						<label>Password</label>
						<input class="form-control" placeholder="Enter your password" type="password" name="password">
						<p class="text-danger mt-3"><?php if ($validation->hasError('password')) { echo $validation->getError('password');}?></p>

					</div>
					<button type="submit" class="btn ripple btn-main-primary btn-block">Create Account</button>
				</form>
				<div class="mt-3 text-center">
					<p class="mb-0">Already have an account? <a href="#0">Sign In</a></p>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Row -->

</div>
<!-- End Page -->

<?= $this->endsection('content') ?>

<?= $this->section('scripts') ?>

<?= $this->endsection() ?>