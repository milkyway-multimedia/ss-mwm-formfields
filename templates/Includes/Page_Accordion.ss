<% if $startAccordion %>
    <div id="{$startAccordion}" <% if $accordionParentAttributes %>$accordionParentAttributes<% else %>class="<% if $accordionParentClasses %>$accordionParentClasses<% else %>panel-group<% end_if %>"<% end_if %>>
<% end_if %>

<% if $accordions %>
    <% loop $accordions %>
        $FieldHolder
    <% end_loop %>
<% else %>
<% if not $bottomOnly %>
    <div<% if $accordionAttributes %>$accordionAttributes<% else %> id="{$accordionName}" class="panel panel-default" role="panel"<% end_if %>>
		<div class="panel-heading">
			<h4 id="{$accordionName}-Title" role="title">
				<a data-toggle="collapse"<% if $accordionParent %> data-parent="#{$accordionParent}"<% end_if %>
				   href="#{$accordionName}-Body">
                    $accordionTitle
				</a>
			</h4>
		</div>
	<div id="{$accordionName}-Body"
	     class="panel-collapse collapse<% if $accordionActive %> active in<% end_if %>">
	<div id="{$accordionName}-Body-Content" class="panel-body" role="content">
<% end_if %>

<% if $accordionFields %>
    <% loop $accordionFields %>
        $FieldHolder
    <% end_loop %>
<% else_if $accordionIframe %>
		<iframe src="$accordionIframe" class="modal-iframe" frameborder="0">
            <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
		</iframe>
<% else %>
    $accordionBody
<% end_if %>
<% end_if %>

<% if not $topOnly %>
	</div>
	</div>
	</div>
<% end_if %>

<% if $endAccordion %>
	</div>
<% end_if %>