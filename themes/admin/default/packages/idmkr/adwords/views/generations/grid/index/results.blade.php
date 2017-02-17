<script type="text/template" data-grid="generation" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input content="id" input data-grid-checkbox="" name="entries[]" type="checkbox" value="<%= r.id %>"></td>
			<td><a href="<%= r.edit_uri %>"><%= r.id %></a></td>
			<td><%= r.adwords_batch_job_id %></td>
			<td><%= r.templategroupeannonce_id %></td>
			<td><%= r.feed_id %></td>
			<td><%= r.adwords_feed_id %></td>
			<td><%= r.operations_count %></td>
			<td><%= r.status %></td>
			<td><%= r.ended_at %></td>
			<td><%= r.adgroups_count %></td>
			<td><%= r.spare_ads_count %></td>
			<td><%= r.customized_ads_count %></td>
			<td><%= r.keywords_count %></td>
			<td><%= r.feed_updates_count %></td>
			<td><%= r.enabled %></td>
			<td><%= r.created_at %></td>
		</tr>

	<% }); %>

</script>
