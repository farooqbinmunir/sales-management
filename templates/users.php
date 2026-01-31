<div class="user-page">

	<button id="addNewUser" class="add-new">Add New User</button>

	<div class="product-form-wrap">

		<h4 class="product-table-heading">

			Add Users Table

			<span class="closeUserForm">&times;</span>

		</h4>

		<form action="" id='add_stock_form'>

			<div class="form-inner">

				<div class="">

					<label for="user_name">User Name</label>

					<input type="text" name="user_name" id="user_name" required>

				</div>

				<div class="">

					<label for="user_email">User Email</label>

					<input type="email" name="user_email" id="user_email" required>

				</div>

				<div class="">

					<label for="user_phone">User Phone</label>

					<input type="number" name="user_phone" id="user_phone" required>

				</div>

				<div class="">

					<label for="user_cnic">User CNIC</label>

					<input type="text" name="user_cnic" id="user_cnic">

				</div>

				<div class="">

					<label for="user_address">User Address</label>

					<input type="text" name="user_address" id="user_address" required>

				</div>

			</div>

			<div class="submit-wrap">

				<input type="submit" id="add_user" value="Add User" class="save-btn">

			</div>

		</form>

	</div>

	<div class="table-wrap">

	<table>

		<thead>

			<tr>

				<th class="sr-Number">#</th>

				<th class="user_name">User Name</th>

				<th class="user_email">User Email</th>

				<th class="user_phone">User Phone</th>

				<th class="user_cnic">User CNIC</th>

				<th class="user_address">User Address</th>

				<th class="user_action">Action</th>

			</tr>

		</thead>

	<tbody>

			<?php

			global $wpdb;

			$table = $wpdb->prefix . 'sms_users';

			$sql = "SELECT * FROM $table";

			$users = $wpdb->get_results($sql);

			if(count($users) > 0){

				$i = 1;

				foreach($users as $user){

					$user_id = $user->user_id;

					$user_name = $user->name;

					$user_email = $user->email;

					$user_phone = $user->phone;

					$user_user_cnic = $user->user_cnic;

					$user_address = $user->address;

					?>

					<tr data-id="<?php echo $user_id; ?>">

						<td class="sr-Number"><?php echo $i++; ?></td>

						<td class="user_name"><?php echo $user_name; ?></td>

						<td class="user_email"><?php echo $user_email; ?></td>

						<td class="user_phone"><?php echo $user_phone; ?></td>

						<td class="user_cnic"><?php echo $user_user_cnic; ?></td>

						<td class="user_address"><?php echo $user_address; ?></td>

						<td class="user_action">

							<button id='user_reset_btn' class='reset-btn'>Reset</button>

							<button id='user_delete_btn' class='delete-btn'>Delete</button>

						</td>

					</tr>

					<?php

				}

			}else{ ?>

				<tr>

					<td colspan="6">No User found.</td>

				</tr>                               

				<?php

				}

			?>

		</tbody>

	</table>

</div>