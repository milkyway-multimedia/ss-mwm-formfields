<% if $ModalForms %>
    <% loop $ModalForms %>
        $forTemplate
    <% end_loop %>
<% end_if %>

<% if $Modals %>
    <% loop $Modals %>
        $Body
    <% end_loop %>
<% end_if %>

<% include Page_Modal modalName='Default' %>