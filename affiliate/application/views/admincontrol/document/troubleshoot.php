<div class="row product-page">
	<div class="col-12">
		<div class="card">
			<div class="card-header bg-secondary text-white">
				<h5 class="mb-0"><?= __('admin.troubleshoot') ?></h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped align-middle">
						<thead>
							<tr>
								<th scope="col">Id</th>
								<th scope="col">Type</th>
								<th scope="col">Issue</th>
								<th scope="col">Solution</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>#1</td>
								<td>403</td>
								<td># Can't save video links and images in the editor.</td>
								<td>The issue is server-side configuration. ModSecurity is probably enabled and it creates issues. Need to disable it and refresh the site and try again.</td>
							</tr>
							<tr>
								<td>#2</td>
								<td>Warning: IP Api</td>
								<td># Warning: IP Api Not Working | Extension php_curl.</td>
								<td>The issue is with the IP API external service. There is nothing to do, and it is not a code issue. Once the service is back (usually in a few minutes), the error message will disappear.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>