var j = jQuery.noConflict();
var Configuration = Class.create();

Configuration.prototype = {
    initialize: function (options) {
        this.containerId = options.containerId;
        this.redirectedUrl = options.redirected_url;
        this.tableCounter = 0;
        this.rowCounter = 1;
        this.isFirstRowAddedByScript = false;
        this.loadUploadContainer();
        this.formKey = options.form_key;
    },

    loadUploadContainer: function () {
        var self = this;
        j("#add_new_row_btn").on("click", function (event) {
            event.preventDefault();
            self.addNewTable();
        });

        j("#save_configuration_btn").on("click", function (event) {
            event.preventDefault();
            self.saveConfiguration();
        });
    },

    addNewTable: function () {
        var self = this;
        self.rowCounter = 1;
        var tableContainer = j("#main_container");
        var table = j("<table></table>").attr("id", "table_" + self.tableCounter).appendTo(tableContainer);
        var tableHeader = ["Condition", "Condition Operator", "Condition Value", "Actions", "Dispatch Event"];
        var tr = j("<tr></tr>");
        tableHeader.forEach(function (header) {
            tr.append(j("<th></th>").text(header));
        });
        table.append(tr);

        if (!self.isFirstRowAddedByScript) {
            console.log('hey');
            self.addRow(self.tableCounter); // Add row only if the first row is not added by the script
        }
        self.tableCounter++;
    },

    addRow: function (tableId) {
        var self = this;
        var table = j("#table_" + tableId);
        var tr = j("<tr></tr>").attr("id", "row_" + self.rowCounter);

        tr.append(j("<td></td>").append(self.createDropDown(["subject", "from", "to"], "condition_" + self.rowCounter)));
        tr.append(j("<td></td>").append(self.createDropDown(["=", "%Like%", "Like", ">=", "<=", "!="], "operator_" + self.rowCounter)));
        tr.append(j("<td></td>").append(j("<input>").attr({ type: "text", name: "value_" + self.rowCounter })));

        var td = j("<td></td>");
        var addButton = j("<button></button>").text("Add").addClass("add-button").on("click", function (event) {
            event.preventDefault();
            self.handleAdd(this, tableId);
        });
        td.append(addButton);

        // if (self.rowCounter > 1) {
            var removeButton = j("<button></button>").text("Delete").addClass("remove-button").on("click", function (event) {
                event.preventDefault();
                self.handleDelete(this);
            });
            td.append(removeButton);
        // }

        tr.append(td);

        if (self.rowCounter === 1) {
            var dispatchEventTd = j("<td></td>").attr("id", "dispatch_event_" + tableId).attr("rowspan", 1).append(j("<input>").attr({ type: "text", id: "dispatchevent", name: "event_" + self.tableCounter }));
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

        row.remove();

        if (table.find("tr").length === 1) { // Check if only header row remains
            table.remove();
        } else {
            self.updateRowspan(table.attr("id").split("_")[1]);
        }
    },

    updateRowspan: function (tableId) {
        var table = j("#table_" + tableId);
        var rowCount = table.find("tr").length - 1; // Subtract header row
        var dispatchEventTd = j("#dispatch_event_" + tableId);
        if (dispatchEventTd.length) {
            dispatchEventTd.attr("rowspan", rowCount);
            table.find("tr:gt(1)").each(function () { // move dispatch event cell to the first row after header
                if (j(this).find("#dispatch_event_" + tableId).length) {
                    j(this).find("#dispatch_event_" + tableId).remove();
                }
            });
            if (rowCount > 0) {
                table.find("tr:eq(1)").append(dispatchEventTd); // ensure dispatch event cell is always in the first row after header
            }
        }
    },

    saveConfiguration: function () {
        var self = this;
        var tables = [];
        j("#main_container table").each(function (tableIndex) {
            var table = [];
            j(this).find("tr:gt(0)").each(function () {
                var row = { 
                    group_id: tableIndex,
                    condition_name: j(this).find("select[name^='condition_']").val(),
                    operator: j(this).find("select[name^='operator_']").val(),
                    value: j(this).find("input[name^='value_']").val(),
                    event_name: j(this).closest("table").find("input[name^='event_']").val() // Find event name from table
                };
                table.push(row);
            });
            tables.push(table);
        });
    
        var url = window.location.href;
        var urlParts = url.split('/');
        var idIndex = urlParts.indexOf('id');
        if (idIndex !== -1 && idIndex < urlParts.length - 1) {
            var configId = urlParts[idIndex + 1];
            console.log(configId);
        } else {
            console.log('id parameter not found in the URL');
        }
    
        var form_key = this.formKey;
        console.log(form_key);
        j.ajax({
            type: "POST",
            url: this.redirectedUrl,
            data: { tables: JSON.stringify(tables), form_key: form_key, config_id: configId },
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
