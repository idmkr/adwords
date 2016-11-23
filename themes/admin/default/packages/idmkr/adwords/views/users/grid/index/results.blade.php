<script type="text/template" data-grid="user" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input content="id" input data-grid-checkbox="" name="entries[]" type="checkbox" value="<%= r.id %>"></td>
			<td><a href="<%= r.edit_uri %>"><%= r.id %></a></td>
			<td><%= r.client_manager_id %></td>
			<td><%= r.user_id %></td>
			<td><%= r.client_customer_id %></td>
			<td><%= r.created_at %></td>
		</tr>

	<% }); %>

</script>
