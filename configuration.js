var j = jQuery.noConflict();
var Configuration = Class.create();

Configuration.prototype = {
    initialize: function (options) {
        // this.containerId = options.containerId;
        this.redirectedUrl = options.redirected_url;
        this.loadUrl = options.loadUrl;
        this.tableCounter = 0;
        this.rowCounter = 1;
        this.isFirstRowAddedByScript = false;
        this.formKey = options.form_key;
        this.setupEventHandlers();
        this.loadConfigurationData();
    },

    setupEventHandlers: function () {
        var self = this;
        j("#add_new_table_btn").on("click", function (event) {
            event.preventDefault();
            self.addNewTable();
        });

        j("#save_configuration_btn").on("click", function (event) {
            event.preventDefault();
            self.saveConfiguration();
        });
    },

    loadConfigurationData: function () {
        var self = this;
        var url = window.location.href;
        var urlParts = url.split('/');
        var idIndex = urlParts.indexOf('id');
        if (idIndex !== -1 && idIndex < urlParts.length - 1) {
            var configId = urlParts[idIndex + 1];
            j.ajax({
                url: self.loadUrl,
                data: { config_id: configId },
                success: function (response) {
                    var tables = JSON.parse(response);
                    console.log(tables);
                    if (tables.status !== 'error') {
                        tables.forEach(function (table) {
                            self.isFirstRowAddedByScript = true;
                            self.addNewTable();
                            table.forEach(function (row) {
                                var lastTableIndex = self.tableCounter - 1;
                                var table = j("#table_" + lastTableIndex);
                                var tr = j("<tr></tr>");
                                tr.append(j("<td></td>").append(self.createDropDown(["subject", "from", "to"], "condition_name_" + row.group_id).val(row.condition_name)));
                                tr.append(j("<td></td>").append(self.createDropDown(["=", "%Like%", "Like", ">=", "<=", "!="], "operator_" + row.group_id).val(row.operator)));
                                tr.append(j("<td></td>").append(j("<input>").attr({ type: "text", name: "value_" + row.group_id }).val(row.value)));
                                tr.append(j("<td></td>").append(j("<button></button>").text("Add").addClass("add-button").on("click", function (event) {
                                    event.preventDefault();
                                    self.handleAdd(this, lastTableIndex);
                                }).add(j("<button></button>").text("Delete").addClass("remove-button").on("click", function (event) {
                                    event.preventDefault();
                                    self.handleDelete(this);
                                }))));
                                tr.append(j("<td></td>").attr("id", "dispatch_event_" + lastTableIndex).attr("rowspan", 1).append(j("<input>").attr({ type: "text", id: "dispatchevent", name: "event_" + row.group_id }).val(row.event_name)));
                                table.append(tr);
                                self.updateRowspan(lastTableIndex);
                            });
                        });
                    }
                }
            });
        }
    },

    addNewTable: function () {
        var self = this;
        self.rowCounter = 1;
        var tableContainer = j("#main_container");
        var table = j("<table></table>").attr("id", "table_" + self.tableCounter).appendTo(tableContainer);
        var tableHeader = ["Condition", "Condition Operator", "Condition Value", "Actions", "Dispatch Event", "Delete Table"];
        var tr = j("<tr></tr>");
        tableHeader.forEach(function (header) {
            tr.append(j("<th></th>").text(header));
        });
        table.append(tr);
    
        if (!self.isFirstRowAddedByScript) {
            // Add row only if the first row is not added by the script
            self.addRow(self.tableCounter);
        }
    
        var deleteButton = j("<button></button>").text("Delete Table").addClass("delete-table-button").data("table-id", self.tableCounter).on("click", function() {
            var tableId = j(this).data("table-id");
            self.handleDeleteTable(tableId);
        });
        tr.append(deleteButton);
    
        self.tableCounter++;
    },
    
    handleDeleteTable: function (tableId) {
        var self = this;
        j("#table_" + tableId).remove();
        self.updateTableCounter();
    },
    
    
    updateTableCounter: function () {
        var self = this;
        self.tableCounter--;
        // Update IDs of remaining tables and dispatch event fields
        j("#main_container table").each(function (index) {
            var newTableId = "table_" + index;
            j(this).attr("id", newTableId);
            j(this).find("button.delete-table-button").attr("data-table-id", index);
            j(this).find("input[name^='event_']").attr("name", "event_" + index);
            j(this).find("td[id^='dispatch_event_']").attr("id", "dispatch_event_" + index);
            self.updateRowspan(index);
        });
        // // Update group ids in data array
        // self.updateDataArray();
    },
    
    

    addRow: function (tableId) {
        var self = this;
        var table = j("#table_" + tableId);
        var tr = j("<tr></tr>").attr("id", "row_" + self.rowCounter);

        tr.append(j("<td></td>").append(self.createDropDown(["subject", "from", "to"], "condition_name_" + self.rowCounter)));
        tr.append(j("<td></td>").append(self.createDropDown(["=", "%Like%", "Like", ">=", "<=", "!="], "operator_" + self.rowCounter)));
        tr.append(j("<td></td>").append(j("<input>").attr({ type: "text", name: "value_" + self.rowCounter })));

        var td = j("<td></td>");
        var addButton = j("<button></button>").text("Add").addClass("add-button").on("click", function (event) {
            event.preventDefault();
            self.handleAdd(this, tableId);
        });
        td.append(addButton);

        var removeButton = j("<button></button>").text("Delete").addClass("remove-button").on("click", function (event) {
            event.preventDefault();
            self.handleDelete(this);
        });
        td.append(removeButton);

        tr.append(td);

        if (self.rowCounter === 1) {
            var dispatchEventTd = j("<td></td>").attr("id", "dispatch_event_" + tableId).attr("rowspan", 1).append(j("<input>").attr({ type: "text", id: "dispatchevent_" + tableId, name: "event_" + self.tableCounter }));
            tr.append(dispatchEventTd);
        }

        table.append(tr);
        self.updateRowspan(tableId);
        self.rowCounter++;
    },

    createDropDown: function (options, name) {
        var select = j("<select></select>").attr("name", name);
        options.forEach(function (option) {
            select.append(j("<option></option>").val(option).text(option));
        });
        return select;
    },

    handleAdd: function (button, tableId) {
        var self = this;
        var currentRow = j(button).closest("tr");
        var newRow = currentRow.clone();
        newRow.find("input, select").each(function () {
            var oldName = j(this).attr("name");
            var newName = oldName.replace(/\d+$/, '') + self.rowCounter;
            j(this).attr("name", newName).val("");
        });
        newRow.find(".add-button").on("click", function (event) {
            event.preventDefault();
            self.handleAdd(this, tableId);
        });
        newRow.find(".remove-button").on("click", function (event) {
            event.preventDefault();
            self.handleDelete(this);
        });

        currentRow.after(newRow);
        self.updateRowspan(tableId);
        self.rowCounter++;
    },

    handleDelete: function (button) {
        var self = this;
        var row = j(button).closest("tr");
        var table = row.closest("table");
        var tableId = table.attr("id").split("_")[1];
        var rowCount = table.find("tr").length;

        // Check if this row contains the dispatch event cell
        if (row.find("#dispatch_event_" + tableId).length > 0) {
            if (rowCount > 2) { // More than one row besides the header
                var nextRow = row.next();
                nextRow.append(row.find("#dispatch_event_" + tableId).detach()); // Move dispatch event cell to next row
                row.remove();
                self.updateRowspan(tableId);
            } else {
                alert("You cannot delete the last row. If you want to remove the entire table, use the 'Delete Table' button.");
            }
        } else {
            row.remove();
            self.updateRowspan(tableId);
        }
    },

    updateRowspan: function (tableId) {
        var table = j("#table_" + tableId);
        var rowCount = table.find("tr").length - 1; // Subtract header row
        var dispatchEventTd = j("#dispatch_event_" + tableId);
        if (dispatchEventTd.length) {
            dispatchEventTd.attr("rowspan", rowCount);
            table.find("tr:gt(1)").each(function () { // Move dispatch event cell to the first row after header
                if (j(this).find("#dispatch_event_" + tableId).length) {
                    j(this).find("#dispatch_event_" + tableId).remove();
                }
            });
            if (rowCount > 0) {
                table.find("tr:eq(1)").append(dispatchEventTd); // Ensure dispatch event cell is always in the first row after header
            }
        }
    },

    saveConfiguration: function () {
        var self = this;
        var tables = [];
        j("#main_container table").each(function (tableIndex) {
            var table = [];
            j(this).find("tr:gt(0)").each(function () {
                console.log(j(this).find("select[name^='condition_name_']").val());
                var row = {
                    group_id: tableIndex,
                    condition_name: j(this).find("select[name^='condition_name_']").val(),
                    operator: j(this).find("select[name^='operator_']").val(),
                    value: j(this).find("input[name^='value_']").val(),
                };
                table.push(row);
            });
            var event_name = j(this).find("input[name^='event_']").val(); // Get event name from the table
            tables.push({ rows: table, event_name: event_name });
        });

        var url = window.location.href;
        var urlParts = url.split('/');
        var idIndex = urlParts.indexOf('id');
        var configId = idIndex !== -1 && idIndex < urlParts.length - 1 ? urlParts[idIndex + 1] : null;
        console.log(JSON.stringify(tables));
        j.ajax({
            type: "POST",
            url: self.redirectedUrl,
            data: { tables: JSON.stringify(tables), form_key: self.formKey, config_id: configId },
            success: function (response) {
                console.log(response);
                console.log("Configuration saved successfully!");
            },
            error: function (error) {
                console.error("Error saving configuration:", error);
            }
        });
    }
};
