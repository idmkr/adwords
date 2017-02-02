<script type="text/template" data-grid="generationfeeditemadgroup" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input content="id" input data-grid-checkbox="" name="entries[]" type="checkbox" value="<%= r.id %>"></td>
			<td><a href="<%= r.edit_uri %>"><%= r.id %></a></td>
			<td><%= r.feed_item_id %></td>
			<td><%= r.generation_id %></td>
			<td><%= r.adwords_adgroup_id %></td>
			<td><%= r.adwords_adgroup_status %></td>
			<td><%= r.adwords_adgroup_ads_count %></td>
			<td><%= r.adwords_adgroup_keywords_count %></td>
			<td><%= r.created_at %></td>
		</tr>

	<% }); %>

</script>
