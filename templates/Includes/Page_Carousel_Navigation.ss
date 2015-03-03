<% if $carouselNavigation %>
	<ol class="carousel-indicators $carouselNavigationClasses">
		<li data-target="#{$carouselName}" data-slide="prev" title="<% _t('PREV', 'Previous') %>" class="prev"></li>
        <% loop $carouselNavigation %>
			<li data-target="#{$carouselName}" data-slide-to="$Pos(0)" title="$carouselTitle"<% if $carouselActive %> class="active"<% end_if %>></li>
        <% end_loop %>
		<li data-target="#{$carouselName}" data-slide="next" title="<% _t('NEXT', 'Next') %>" class="next"></li>
	</ol>
<% end_if %>