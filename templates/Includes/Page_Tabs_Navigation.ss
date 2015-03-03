<% if $tabNavigation %>
    <ul class="nav <% if $tabPills %>nav-pills<% else %>nav-tabs<% end_if %>">
        <% loop $tabNavigation %>
	        <li<% if $tabActive %> class="active"<% end_if %>><a href="#{$tabName}" data-target="#{$tabName}" data-toggle="tab">$tabTitle</a></li>
        <% end_loop %>
    </ul>
<% end_if %>