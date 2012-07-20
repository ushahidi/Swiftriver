<div id="content" class="settings filters cf">
	<div class="center">
		<div class="col_12">
			<div class="settings-toolbar">
				<p class="button-blue button-small create">
					<a href="#" class="modal-trigger"><span class="icon"></span><?php echo __("Add Filter"); ?></a>
				</p>
			</div>

			<div class="alert-message blue" style="display: none;">
				<p><strong>No Filters</strong>
				<?php 
					echo __("You can add new filters by selecting the \"Add Filters\" button above. 
					    These filters will be applied drops before they hit your river. 
					    Only drops that match the filters you have specified will appear in the river"); 
				?>
				</p>
			</div>

			<!-- FILTERS WILL GO HERE -->
		</div>
	</div>
</div>

<script type="text/template" id="add-filters-dialog-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9"><h1><?php echo __("Add Filter"); ?></h1></div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#"><span class="icon"></span><?php echo __("Close"); ?></a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body select-list">
		<form class="filters">
			<!-- AVAILABLE FILTERS WILL GO HERE -->
		</form>
	</div>
</script>

<script type="text/template" id="add-filters-dialog-item-template">
	<input type="checkbox" name="<%= filter %>" <%= added ? "checked" : "" %> />
	<%= options.name %>
</script>

<!-- Filter template (A filter and it's filter parameters) -->
<script type="text/template" id="filter-template">
	<header class="cf">
		<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay"><?php echo __("Remove"); ?></span></a>
		<div class="property-title">
			<h1><%= name %></h1>
			<div class="popover add-parameter">
				<p class="button-white has-icon add">
					<a href="#" class="parameter-trigger"><span class="icon"></span><%= "Add " + label %></a>
				</p>
			</div>
		</div>
	</header>
	<section class="property-parameters filter-parameters">
	</section>
</script>

<!-- Filter parameter template  -->
<script type="text/template" id="filter-parameter-template">
	<label>
		<p class="field"><%= label %></p>
		<%= input %>
		<p class="remove-small actions">
			<span class="icon"></span><span class="nodisplay"><?php echo __("Remove"); ?></span>
		</p>
		<% if (renderMode == "view" && value !== null) { %>
			<p class="actions"><span class="button-blue"><a class="edit" href="#"><?php echo __("Edit"); ?></a></span></p>
		<% } %>
		<% if (renderMode == "edit" && value !== null) { %>
			<p class="actions"><span class="button-blue"><a class="cancel" href="#"><?php echo __("Cancel"); ?></a></span></p>
		<% } %>
		<p class="actions" style="display:none;"><span class="button-blue"><a class="save"><?php echo __("Save"); ?></a></span></p>
	</label>
	<div style="clear: both;"></div>
</script>

<!-- Template for rendering a filter parameter in edit mode  -->
<script type="text/template" id="edit-filter-parameter-template">
	<% if (type == "text") { %>
		<input type="text" name="<%= filter %>" placeholder="<%= placeholder %>" value="<%= value %>" />
	<% } else if (type == "map") { %>

	<% } %>
</script>

<!-- Template for rendering a filter parameter in read mode-->
<script type="text/template" id="view-filter-parameter-template">
	<p class="field-text"><%= value %></p>
</script>



<script type="text/javascript">
$(function() {
	// Base fetch URL
	var baseURL = "<?php echo $base_url; ?>";

	// Filter configuration model and collection
	var FilterConfig = Backbone.Model.extend();

	var FilterConfigList = Backbone.Collection.extend({
		model: FilterConfig,

		// Gets the configuration for a given filter item
		getFilterConfig: function(filter) {
			return this.find(function(config) {
				return config.get("filter") == filter
			}, this);
		},

		// Gets the value of a single config item for the specified
		// filter
		getFilterConfigOption: function(filter, key) {
			var config = this.getFilterConfig(filter);
			return config.get('options')[key];
		}
	});

	var filtersConfig = new FilterConfigList();

	// Bootstrap the filters configuration
	filtersConfig.reset(<?php echo $filters_config; ?>);


	// Filters model and collection for the filters for the river
	var Filter = Backbone.Model.extend({
		toggleEnabled: function() {
			this.save({enabled: !this.get("enabled")});
		}
	});

	var FiltersList = Backbone.Collection.extend({
		model: Filter,

		url: baseURL + "/manage",

		// Get the filter (model) with the specified name
		getFilter: function(filterName) {
			return this.find(function(filter) {
				return filter.get("filter") === filterName;
			}, this);
		},

		// Gets the number of active filters
		activeFilters: function() {
			return this.filter(function(f) {
				return f.get("enabled");
			}, this).length;
		}
	});

	var filters = new FiltersList();

	// Single filter item for a river and a collection of the same
	var FilterParameter = Backbone.Model.extend();

	var FilterParameterList = Backbone.Collection.extend({
		model: FilterParameter
	});

	// Single filter in the "Add Filters" modal dialog
	var AddFiltersItemView = Backbone.View.extend({

		tagName: "label",

		template: _.template($("#add-filters-dialog-item-template").html()),

		events: {
			"change input": "toggleFilter"
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},

		toggleFilter: function(e) {
			var filter = filters.getFilter(this.model.get("filter"));
			if (filter !== undefined) {
				// Filter exists, toggle its enabled state
				filter.toggleEnabled();
			} else {
				// Filter doesn't exist in the river, create it
				filters.create({filter: this.model.get("filter")}, {
					wait: true,
				});
			}

			return false;
		}
	});

	// The "Add Filters" modal dialog
	var AddFiltersView = Backbone.View.extend({

		tagName: "article",

		className: "modal",

		template: _.template($("#add-filters-dialog-template").html()),

		render: function() {
			this.$el.html(this.template()),

			// Render the filters list
			filtersConfig.each(function(config) {
				// Set flag to denote whether the filter has been added to the river
				var filter  = filters.getFilter(config.get("filter"));
				config.set("added", filter !== undefined && filter.get("enabled"));

				var view = new AddFiltersItemView({model: config});
				this.$("form.filters").append(view.render().el);
			}, this);

			return this;
		},
	});

	/**
	 * Single filter parameter view
	 * This view is contained in FilterView
	 */
	var FilterParameterView = Backbone.View.extend({

		tagName: "div",

		className: "parameter",

		template: _.template($("#filter-parameter-template").html()),

		events: {
			"click .remove-small": "remove",
			"click .button-blue a.save": "save",
			"click .button-blue a.edit": "edit",
			"click .button-blue a.cancel": "cancel",
			"keyup input": "toggleSaveButton"
		},

		initialize: function() {
			this.renderMode = "view";
			this.savingMode = false;
		},

		getInputField: function() {
			var input = null;
			if (this.model.get("value") !== null && this.renderMode == "view") {
				input = _.template($("#view-filter-parameter-template").html());
				return input({value: this.model.get("value")});
			}
			
			input = _.template($("#edit-filter-parameter-template").html());

			var filterName = this.model.get("filter");
			var inputType = filtersConfig.getFilterConfigOption(filterName, "type");
			return input({
				type: inputType,
				placeholder: inputType === "text" ? filtersConfig.getFilterConfigOption(filterName, "placeholder") : "",
				filter: filterName,
				value: this.model.get("value")
			});

		},

		render: function() {
			var data = this.model.toJSON();
			data.label = filtersConfig.getFilterConfigOption(this.model.get("filter"), "label");
			data.input = this.getInputField();
			data.renderMode = this.renderMode;
			this.$el.html(this.template(data));
			return this;
		},

		// Deletes a filter parameter
		remove: function() {
			if (this.model.isNew()) {
				// Not synced yet, just remove from view
				this.$el.fadeOut("slow");
			} else {
				var view = this;
				this.model.destroy({
					wait: true,
					success: function() {
						view.$el.fadeOut("slow");
					}
				});
			}
		},

		// Callback function to trigger saving of a filter parameter
		save: function(e) {
			// Enter saving mode
			this.savingMode = true;
			
			var view = this;
			var value = this.$("input").val();

			this.model.save({value: value}, {
				wait: true,
				success: function() {
					$(this).remove();
					view.renderMode = "view";
					view.render();
				},

				// Error handling
				error: function(model, response) {
					var message = "Oops, unable to save. Try again";
					if (response.status == 400) {
						message = JSON.parse(response.responseText)["error"];
					}
					var error_msg = $('<span class="error-message">' + message + '</span>');
					loading_msg.replaceWith(error_msg).remove();
					view.$("input, select").removeAttr("disabled");
				}
			});

			this.savingMode = false;
			return false;
		},

		// Makes the filter parameter editable
		edit: function(e) {
			this.renderMode = "edit";
			this.render();
			return false;
		},

		// Cancels editing mode and restores the parameter view
		// to read-mode
		cancel: function (e) {
			// Only process cancel action when we're not in saving mode
			if (!this.savingMode) {
				this.renderMode = "view";
				this.render();
			}
			return false;
		},

		// Displays the save button
		showSaveButton: function() {
			this.$("span.error-message").remove();
			this.$("a.save").parents("p.actions").fadeIn("slow");
		},

		// Hides the save button
		hideSaveButton: function() {
			var newValue = this.$("input[type=text]").val();
			if( ! newValue || newValue == this.model.get("value")  ) {
				this.$("a.save").parents("p.actions").fadeOut();
			}
		},

		toggleSaveButton: function(e) {
			if(e.which == 13){
				this.save();
				return false;
			} else {
				var newValue = $.trim(this.$("input[type=text]").val());
				if(newValue != "" && newValue && newValue != this.model.get("value")) {
					this.showSaveButton();
				} else {
					this.hideSaveButton();
				}
			}

			return false;
		}
	});

	/**
	 * Single filter view
	 * This view renders all the parameters for a given filter .e.g for the places filter,
	 * this view shall display all the place names for the filter
	 */
	var FilterView = Backbone.View.extend({

		tagName: "article",

		className: "container base",

		template: _.template($("#filter-template").html()),

		events: {
			"click a.remove-large": "confirmDeleteFilter",
			"click a.parameter-trigger": "showAddFilterParameter",
		},

		initialize: function() {
			this.model.on("change:enabled", this.activeChanged, this);

			this.filterParameters = new FilterParameterList();
			this.filterParameters.url = baseURL + "/parameters/" + this.model.get("id");
			this.filterParameters.on("add", this.addFilterParameter, this);
			this.filterParameters.reset(this.model.get("parameters"));
		},

		activeChanged: function(e) {
			if (!this.model.get("enabled")) {
				// Filter no longer active, remove from view
				this.$el.fadeOut("slow");
			} else {
				// Filter is active, add to view
				this.$el.fadeIn("slow");
			}
			return false;
		},

		addFilterParameter: function(parameter) {
			var view = new FilterParameterView({model: parameter});
			this.$("section.filter-parameters").prepend(view.render().el);
		},

		confirmDeleteFilter: function(e) {
			new ConfirmationWindow("Remove this filter?", this.deleteFilter, this).show();
			return false;
		},

		showAddFilterParameter: function(e) {
			this.filterParameters.add(new FilterParameter({
				value: null,
				filter: this.model.get("filter")
			}));
			return false;
		},

		render: function() {
			var configOptions = filtersConfig.getFilterConfig(this.model.get("filter")).get("options");
			this.$el.html(this.template(configOptions));

			// Render the filter parameters
			this.filterParameters.each(this.addFilterParameter, this);

			return this;
		},

		deleteFilter: function() {
			var view = this;
			this.model.destroy();
			view.$el.fadeOut("slow");
		},
	});

	// Main view for the river filters
	var RiverFiltersControl = Backbone.View.extend({

		el: "div.filters",

		events: {
			"click .settings-toolbar p.create a": "showAddFiltersDialog"
		},

		initialize: function() {
			filters.on("add", this.addFilter, this);
			filters.on("reset", this.addFilters, this);

			filters.on("reset", this.checkEmpty, this);
			filters.on("add", this.checkEmpty, this);
			filters.on("remove", this.checkEmpty, this);
			filters.on("change:enabled", this.checkEmpty, this);
		},

		addFilter: function(filter) {
			var view = new FilterView({model: filter});
			this.$("div.col_12").append(view.render().el);
		},

		addFilters: function() {
			filters.each(this.addFilter, this);
		},

		showAddFiltersDialog: function(e) {
			var addFiltersView = new AddFiltersView();
			modalShow(addFiltersView.render().el);
			return false;
		},

		// Verifies whether there are any active filters
		// and toggles the display of the notification message
		checkEmpty: function() {
			if (filters.length && filters.activeFilters()) {
				this.$("div.alert-message").fadeOut("slow");
			} else {
				this.$("div.alert-message").fadeIn("slow");
			}
		}
	});

	// Bootstrap the river filters control
	new RiverFiltersControl();
	filters.reset(<?php echo $filters; ?>);
});
</script>