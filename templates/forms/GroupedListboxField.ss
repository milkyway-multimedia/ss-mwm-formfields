<select $AttributesHTML>
<% loop Options %>
	<% if Options %>
		<optgroup label="$Title">
			<% loop Options %>
			<option value="$Value"<% if Selected %> selected="selected"<% end_if %><% if Disabled %> disabled="disabled"<% end_if %>>$Title</option>
			<% end_loop %>
		</optgroup>
	<% else %>
		<option value="$Value"<% if Selected %> selected="selected"<% end_if %><% if Disabled %> disabled="disabled"<% end_if %>>$Title</option>
	<% end_if %>
<% end_loop %>
</select>
