<ul $AttributesHTML>
    <% if $Children %>
    <% loop $Children %>
        <li class="$CssClasses.ATT">$KeyField.Field<% if $Up.Separator %><span class="mventryfield-separator">$Up.Separator</span><% end_if %>$ValField.Field</li>
    <% end_loop %>
    <% end_if %>
</ul>