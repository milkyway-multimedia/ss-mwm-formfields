<div $AttributesHTML>

</div>

<% if $Inputs %>
    <% loop $Inputs %>
        $Render.Field
    <% end_loop %>
<% end_if %>