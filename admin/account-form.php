<?php
// Included by account.php and by account-save.php on validation failure.
// Expects: $currentUsername.
?>
<form class="project-form" method="post" action="account-save.php">
  <div class="field">
    <label for="username">Username</label>
    <input id="username" name="username" type="text" value="<?= h($currentUsername) ?>" required>
  </div>

  <div class="field">
    <label for="current_password">Current password</label>
    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
  </div>

  <div class="field">
    <label for="new_password">New password <span style="color:rgba(255,255,255,0.5)">(leave blank to keep current password)</span></label>
    <input id="new_password" name="new_password" type="password" autocomplete="new-password">
  </div>

  <div class="field">
    <label for="new_password_confirm">Confirm new password</label>
    <input id="new_password_confirm" name="new_password_confirm" type="password" autocomplete="new-password">
  </div>

  <button type="submit" class="btn">Save changes</button>
</form>
