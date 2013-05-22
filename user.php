<?php
require_once('inc/session.php');
require_once('inc/core.php');
require_once('inc/utils.php');

$changedPassword = false;
if ($_SESSION['source'] == 'database' && isset($_POST['password']) && $_POST['password'] == $_POST['password2']) {
	if (!isset($settings['database']['dsn']))
		die('No database configured');
	$dbh = new PDO($settings['database']['dsn'], $settings['database']['user'], $settings['database']['password']);
	$statement = $dbh->prepare("UPDATE users SET password = :password WHERE username = :username;");
	$statement->execute(array(':username' => $_SESSION['username'], ':password' => crypt($_POST['password'])));
	$changedPassword = true;
}

$title = 'Account';
require_once('inc/header.php');
?>
		</div>
		<?php if ($changedPassword) { ?>
		<div class="message pad ok">Password changed</div>
		<?php } ?>
		<div class="halfpages">
			<div class="halfpage">
				<fieldset>
					<legend>Permissions</legend>
					<p>You are authorized to view messages sent from/to the following users/domains:</p>
					<ul>
					<?php
						$r = 0;
						if (is_array($_SESSION['access']['mail'])) { ?>
							<?php
							foreach($_SESSION['access']['mail'] as $mail) {
								++$r;
								echo "<li>";
								p($mail);
							}
						}
						if (is_array($_SESSION['access']['domain'])) { ?>
							<?php
							foreach($_SESSION['access']['domain'] as $domain) {
								++$r;
								echo "<li>";
								p($domain);
							}
						}
						if ($r == 0)
							echo "<li>No restrictions (you can view everything)";
					?>
					</ul>
				</fieldset>
			</div>
			<div class="halfpage">
				<fieldset>
					<legend>Change password</legend>
			<?php if ($_SESSION['source'] == 'database') { ?>
					<form method="post">
						<div>
							<label>Password</label>
							<input type="password" name="password">
						</div>
						<div>
							<label>Repeat password</label>
							<input type="password" name="password2">
						</div>
						<div>
							<label></label>
							<button type="submit">Change</button>
						</div>
					</form>
				</p>
			<?php } else { ?>
				<p>
					User authenticated using <?php p($_SESSION['source']); ?> and can not change the password from this page.
				</p>
			<?php } ?>
			</fieldset>
			</div>
		</div>
<?php require_once('inc/footer.php'); ?>
