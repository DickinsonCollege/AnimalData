function toggle(id, label) {
   var disp = $("#" + id + "_div").css('display');
   if (disp == "none") {
      $("#" + id + "_div").css('display', 'block');
      $("#" + id + "_btn").prop("value", "Hide " + label);
   } else {
      $("#" + id + "_div").css('display', 'none');
      $("#" + id + "_btn").prop("value", "Show " + label);
   }
   $("#" + id + "_btn").button("refresh");
}

function nonAdmin() {
   alert("The functionality you are trying to use is available to " +
         "administrative users only.  Ask an administrative user to " +
         "perform this task for you.");
}

var returnHash = "";
function cancel(loc) {
   if (returnHash != "") {
      location.hash = returnHash;
   } else {
      location.hash = loc;
   }
}

function date_string(id, year) {
   var con = "<div class='ui-block-a' style='width:45%'><select id='" + 
             id + "_month' name='" + id + "_month'>";
   con += "<option value='1'>January</option>\n";
   con += "<option value='2'>February</option>\n";
   con += "<option value='3'>March</option>\n";
   con += "<option value='4'>April</option>\n";
   con += "<option value='5'>May</option>\n";
   con += "<option value='6'>June</option>\n";
   con += "<option value='7'>July</option>\n";
   con += "<option value='8'>August</option>\n";
   con += "<option value='9'>September</option>\n";
   con += "<option value='10'>October</option>\n";
   con += "<option value='11'>November</option>\n";
   con += "<option value='12'>December</option>\n";
   con += "</select></div>";
   con += "<div class='ui-block-b' style='width:25%'><select id='" + 
           id + "_day' name='" + id + "_day'>";
   for (i = 1; i < 32; i++) {
      con += "<option value='" + i + "'>" + i + "</option>\n";
   }
   con += "</select></div>";
   con += "<div class='ui-block-c' style='width:30%'><select id='" + 
          id + "_year' name='" + id + "_year'>";
   var stop = parseInt(year) + 6;
   for (i = year - 10; i < stop; i++) {
      con += "<option value='" + i + "'>" + i + "</option>\n";
   }
   con += "</select></div>";
   return con;
}

function create_date(id, date) {
   var year;
   var mth;
   var day;
   if (date == null) {
      var d = new Date();
      year = d.getFullYear();
      mth = d.getMonth() + 1;
      day = d.getDate();
   } else {
      dtArr = date.split("/");
      mth = parseInt(dtArr[0], 10);
      day = parseInt(dtArr[1], 10);
      year = dtArr[2];
   }
   $("#" + id).html(date_string(id, year));
   $("#" + id + "_month").selectmenu();
   $("#" + id + "_month").val(mth);
   $("#" + id + "_month").selectmenu("refresh");
   $("#" + id + "_day").selectmenu();
   $("#" + id + "_day").val(day);
   $("#" + id + "_day").selectmenu("refresh");
   $("#" + id + "_year").selectmenu();
   $("#" + id + "_year").val(year);
   $("#" + id + "_year").selectmenu("refresh");
}

function logout() {
   return $.ajax({
       type: "POST",
       url: "logout.php",
       cache: false,
       data: "",
       success: function (data, status) {
          location.hash = "#home";
          var con = "<h1>To completely log out, close your browser.</h1><p>" +
             "<form method='POST' data-ajax='false' action='login.php'><input type='submit' " +
             "class='ui-btn' style='width:100%' value='Log In Again'></form>";
          $("#home_div").html(con);
       },
       error: onError
   });
}

function init_group(id, any, grp) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_group.php",
       cache: false,
       data: "",
       success: function (data, status) {
          var con = "";
          if (any) {
              con += "<option value='%'>ALL</option>";
          }
          con += data;
          $("#" + id + "_group").html(con);
          $("#" + id + "_group").selectmenu();
          if (grp != null) {
             $("#" + id + "_group").val(dc(grp));
          }
          $("#" + id + "_group").selectmenu("refresh");
          if (id != "add_breed" && id != "add_subgroup" && id != "sale_report") {
             init_all(id, any);
          }
       },
       error: onError
   });
}

function update_parents(grp, mom, dad) {
   return $.ajax({
       type: "POST",
       url: "get_parents.php?group=" + encodeURIComponent(grp),
       cache: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          $("#birth_mother").html(content['mother']);
          $("#birth_mother").selectmenu();
          $("#birth_mother").val(dc(mom));
          $("#birth_mother").selectmenu("refresh");
          $("#birth_father").html(content['father']);
          $("#birth_father").selectmenu();
          $("#birth_father").val(dc(dad));
          $("#birth_father").selectmenu("refresh");
       },
       error: onError
   });
}

function init_egg_report() {
   create_date("egg_report_from");
   create_date("egg_report_to");
}

function init_egg_input() {
   check_dup("foo"); // only to check database connection
   create_date("egg_input_date");
}

function egg_select(edit, dt, entries) {
   if (entries == 1) {
      return $.ajax({
          type: "POST",
          url: "get_egg_id.php?date=" + encodeURIComponent(dt),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data != "") {
                if (edit) {
                   egg_edit_init(data);
                } else {
                   egg_delete(data);
                }
             } else {
                alert("Error: no such egg record.");
             }
          },
          error: onError
      });
   } else {
      return $.ajax({
          type: "POST",
          url: "select_egg_records.php?date=" + encodeURIComponent(dt) + "&edit="+edit,
          cache: false,
          data: "",
          success: function (data, status) {
             if (data != "") {
                var con = "<h1>Select Egg Record to ";
                if (edit) {
                   con += "Edit";
                } else {
                   con += "Delete";
                }
                con += "</h1>";
                $("#egg_select_header_div").html(con);
                $("#egg_select_table_div").html(data);
                location.hash = "#egg_select";
             } else {
                alert("Error: no such egg records.");
             }
          },
          error: onError
      });
   }
}

function egg_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_egg.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Egg log record successfully deleted.");
                $("#egg_report_form").submit();
                location.hash = "#egg_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function linePlot(divId, title, yLabel, data, ticks) {
   $.jqplot.config.enablePlugins = true;
   var plot1 = $.jqplot(divId, [data], {
      title: title,
      axes:{
         xaxis:{
            renderer:$.jqplot.DateAxisRenderer,
            rendererOptions:{
               tickRenderer:$.jqplot.CanvasAxisTickRenderer
            },
            tickOptions:{
               formatString:'%m/%#d/%y',
               angle:-90,
               fontSize: '12pt'
            },
            numberTicks: ticks
         },
         yaxis:{
            min: 0,
            label: yLabel,
            labelRenderer:$.jqplot.CanvasAxisLabelRenderer,
         },
      },
      series:[{lineWidth:4, markerOptions:{style:'square'}}]
   });
   return plot1;
}

function barChart(divId, title, data, ticks, lab, color) {
   $.jqplot.config.enablePlugins = true;
   var plot1 = $.jqplot(divId, data, {
      seriesDefaults:{
         renderer:$.jqplot.BarRenderer,
         rendererOptions: {fillToZero: true}
      },
      series: lab,
      seriesColors: color,
      grid: {
         // background: 'mintcream'
         background: 'burlywood'
      },
      legend: {
         show: true,
         placement: 'outsideGrid'
      },
      title: title,
      axes: {
         xaxis: {
            renderer: $.jqplot.CategoryAxisRenderer,
            ticks: ticks
         },
         yaxis: {
            min: 0,
            max: 100,
            pad: 1.05,
            tickOptions: {formatString: '%d\%'}
         }
      }
   });
   return plot1;
}

function chart() {
    var s1 = [200, 600, 700, 1000];
    var s2 = [460, -210, 690, 820];
    var s3 = [-260, -440, 320, 200];
    var s4 = [260, 440, 320, 200];
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    var ticks = ['May', 'June', 'July', 'August'];
 
    var plot1 = $.jqplot('chart1', [s1, s2, s3, s4], {
    // The "seriesDefaults" option is an options object that will
    // be applied to all series in the chart.
         seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true}
         },
    // Custom labels for the series are specified with the "label"
    // option on the series option.  Here a series option object
    // is specified for each series.
          series:[
             {label:'Hotel'},
             {label:'Event Regristration'},
             {label:'Airfare'}, 
             {label:'Food'}
          ],
     // Show the legend and put it outside the grid, but inside the
     // plot container, shrinking the grid to accomodate the legend.
     // A value of "outside" would not shrink the grid and allow
     // the legend to overflow the container.
                                                                                                                      legend: {
             show: true,
             placement: 'outsideGrid'
                                                                                                                      },
          axes: {
    // Use a category axis on the x axis and use our custom ticks.
             xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
             },
    // Pad the y axis just a little so bars can get close to, but
    // not touch, the grid boundaries.  1.2 is the default padding.
             yaxis: {
                pad: 1.05,
                tickOptions: {formatString: '$%d'}
             }
          }
     });
}

function replot(plot) {
   plot.replot();
}

function egg_edit_init(id) {
   var formData = new FormData();
   formData.append("id", id);
   $.ajax({
       type: "POST",
       url: "egg_edit_data.php",
       cache: false,
       data: formData,
       success: function (data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var origVals = JSON.parse(data);
      $("#egg_edit_auto_id").val(id);
      create_date("egg_edit_date", humanDate(origVals['coll_date']));
      $("#egg_edit_amt").val(origVals['number']);
      $("#egg_edit_comments").val(dc(origVals['comments']));
      location.hash = "#egg_edit";
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

var wormers = "";
function init_sheep_care_input(id) {
   create_date("sheep_care_input_date", null);
   return $.ajax({
       type: "POST",
       url: "get_sheep_care.php",
       cache: false,
       async: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          var ids = content['animal_id'];
          wormers = content['wormer'];
          $("#" + id + "_id").html(ids);
          $("#" + id + "_id").selectmenu();
          $("#" + id + "_id").selectmenu("refresh");
          $("#" + id + "_wormer").html(wormers);
          $("#" + id + "_wormer").selectmenu();
          $("#" + id + "_wormer").selectmenu("refresh");
          if (id == "sheep_care_input") {
             animal_update('sheep_care_input');
          }
       },
       error: onError
   });
}

function animal_update(pref) {
   var id = $("#" + pref + "_id").val();
   return $.ajax({
       type: "POST",
       url: "animal_update.php?id=" + encodeURIComponent(id),
       cache: false,
       async: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          $("#" + pref + "_group").val(dc(content['animal_group']));
          $("#" + pref + "_name").val(dc(content['name']));
          $("#" + pref + "_mark").val(dc(content['markings']));
          var weight = content['weight'];
          if (weight != "" && weight != "N/A") {
             $("#" + pref + "_weight").val(dc(weight));
          }
          var file = content['filename'];
          if (file == "" || id == null) {
             $("#" + pref + "_picture_div").html("");
          } else {
             $("#" + pref + "_picture_div").html('<div class="ui-field-contain"> ' +
                '<label for="' + pref + '_file">Picture:</label>' +
                '<img style="width:300px" src="' + file + '"/></div>');
          }
       },
       error: onError
   });
}

var reasonId = "";
var wormerId = "";

function add_wormer(hash, id) {
   location.hash = "#add_wormer";
   returnHash = hash;
   wormerId = id;
}

function update_wormer(id) {
   return $.ajax({
       type: "POST",
       url: "get_sheep_care.php",
       cache: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          $("#" + id + "_wormer").html(content['wormer']);
          $("#" + id + "_wormer").selectmenu();
          $("#" + id + "_wormer").selectmenu("refresh");
       },
       error: onError
   });
}

function update_forage(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_forage.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_forage").html(data);
          $("#" + id + "_forage").selectmenu();
          $("#" + id + "_forage").selectmenu("refresh");
       },
       error: onError
   });
}

function update_edit_forage() {
   var forage = $("#edit_forage_forage").val();
   return $.ajax({
       type: "POST",
       url: "forage_edit_data.php?forage=" + encodeURIComponent(forage),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_forage_density").val(origVals['density']);
             $("#edit_forage_active").val(origVals['active']);
             $("#edit_forage_active").selectmenu();
             $("#edit_forage_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function init_edit_forage() {
   update_forage("edit_forage", false);
   update_edit_forage();
}

function update_paddock(id, active) {
   update_forage(id, true);
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_paddock.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_paddock").html(data);
          $("#" + id + "_paddock").selectmenu();
          $("#" + id + "_paddock").selectmenu("refresh");
       },
       error: onError
   });
}

function update_edit_paddock() {
   var paddock = $("#edit_paddock_paddock").val();
   return $.ajax({
       type: "POST",
       url: "paddock_edit_data.php?paddock=" + encodeURIComponent(paddock),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_paddock_size").val(origVals['size']);
             $("#edit_paddock_forage").val(dc(origVals['forage']));
             $("#edit_paddock_forage").selectmenu();
             $("#edit_paddock_forage").selectmenu("refresh");
             $("#edit_paddock_active").val(origVals['active']);
             $("#edit_paddock_active").selectmenu();
             $("#edit_paddock_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function init_edit_paddock() {
   update_paddock("edit_paddock", false);
   update_edit_paddock();
}

function sheep_care_input_validate() {
   var dt = $("#sheep_care_input_date_month").val() + "/" + 
            $("#sheep_care_input_date_day").val()
            + "/" + $("#sheep_care_input_date_year").val();
   var con = "Care Date: " + dt + "\n";
   var id = $("#sheep_care_input_id").val();
   if (id == null) {
      alert("Please select an Animal ID.");
      return false;
   }
   con += "Animal ID: " + id + "\n";
   var eye = $("#sheep_care_input_eye").find(":selected").text();
   con += "Eye (FAMCHA): " + eye + "\n";
   var body = $("#sheep_care_input_body").find(":selected").text();
   con += "Body Condition: " + body + "\n";
   var tail = $("#sheep_care_input_tail").val();
   con += "Tail: " + tail + "\n";
   var nose = $("#sheep_care_input_nose").val();
   con += "Nose: " + nose + "\n";
   var coat = $("#sheep_care_input_coat").val();
   con += "Coat: " + coat + "\n";
   var jaw = $("#sheep_care_input_jaw").find(":selected").text();
   con += "Bottle Jaw: " + jaw + "\n";
   var wormer = $("#sheep_care_input_wormer").val();
   con += "Wormer Given: " + wormer + "\n";
   var quantity = $("#sheep_care_input_wormer_quantity").val();
   con += "Quantity Given: " + quantity + "\n";
   var hoof = $("#sheep_care_input_hoof").val();
   con += "Hoof Condition: " + hoof + "\n";
   var trim = $("#sheep_care_input_trim").val();
   con += "Hoof Trim?: " + trim + "\n";
   var weight = $("#sheep_care_input_weight").val();
   if (weight <= 0) {
      alert("Enter a weight greater than 0.");
      return false;
   }
   var est = $("#sheep_care_input_estimated").val();
   con += "Weight (" + est + "): " + weight + "\n";
   var comments = $("#sheep_care_input_comments").val();
   con += "Comments: " + comments + "\n";
   
   return confirm("Confirm Entry:\n"+con);
}

function update_sheep_care_ids() {
   var dt = $("#sheep_care_input_date_year").val() + "-" + 
            $("#sheep_care_input_date_month").val() + "-" + 
            $("#sheep_care_input_date_day").val();
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_unloved_sheep.php?date=" + encodeURIComponent(dt),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#sheep_care_input_id").html(data);
          $("#sheep_care_input_id").selectmenu();
          $("#sheep_care_input_id").selectmenu("refresh");
       },
       error: onError
   });
}

function init_sheep_care_report() {
   create_date("sheep_care_report_to");
   create_date("sheep_care_report_from");
}

function sheep_care_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_sheep_care.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Sheep/Goat care record successfully deleted.");
                $("#sheep_care_report_form").submit();
                location.hash = "#sheep_care_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function sheep_care_edit_init(id) {
   $.ajax({
       type: "POST",
       url: "sheep_care_edit_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var origVals = JSON.parse(data);
      var sheep = origVals['sheep'];
      var ids = origVals['ids'];
      wormers = origVals['wormer'];
  
      $("#sheep_care_edit_auto_id").val(id);
      create_date("sheep_care_edit_date", humanDate(sheep['care_date']));
      $("#sheep_care_edit_id").selectmenu();
      $("#sheep_care_edit_id").html(ids);
      $("#sheep_care_edit_id").val(dc(sheep['animal_id']));
      $("#sheep_care_edit_id").selectmenu("refresh");
      $("#sheep_care_edit_wormer").selectmenu();
      $("#sheep_care_edit_wormer").html(wormers);
      $("#sheep_care_edit_wormer").val(dc(sheep['wormer']));
      $("#sheep_care_edit_wormer").selectmenu("refresh");
      $("#sheep_care_edit_eye").selectmenu();
      $("#sheep_care_edit_eye").val(sheep['eye']);
      $("#sheep_care_edit_eye").selectmenu("refresh");
      $("#sheep_care_edit_body").selectmenu();
      $("#sheep_care_edit_body").val(sheep['body']);
      $("#sheep_care_edit_body").selectmenu("refresh");
      $("#sheep_care_edit_tail").selectmenu();
      $("#sheep_care_edit_tail").val(sheep['tail']);
      $("#sheep_care_edit_tail").selectmenu("refresh");
      $("#sheep_care_edit_nose").val(dc(sheep['nose']));
      $("#sheep_care_edit_coat").val(dc(sheep['coat']));
      $("#sheep_care_edit_jaw").selectmenu();
      $("#sheep_care_edit_jaw").val(sheep['jaw']);
      $("#sheep_care_edit_jaw").selectmenu("refresh");
      $("#sheep_care_edit_wormer_quantity").val(dc(sheep['wormer_quantity']));
      $("#sheep_care_edit_hoof").selectmenu();
      $("#sheep_care_edit_hoof").val(sheep['hoof']);
      $("#sheep_care_edit_hoof").selectmenu("refresh");
      $("#sheep_care_edit_trim").selectmenu();
      $("#sheep_care_edit_trim").val(sheep['trim']);
      $("#sheep_care_edit_trim").selectmenu("refresh");
      $("#sheep_care_edit_weight").val(sheep['weight']);
      $("#sheep_care_edit_estimated").selectmenu();
      $("#sheep_care_edit_estimated").val(sheep['estimated']);
      $("#sheep_care_edit_estimated").selectmenu("refresh");
      $("#sheep_care_edit_comments").val(dc(sheep['comments']));
   }
          },
          error: onError
      });
}

function edit_move_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_move.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Grazing move record successfully deleted.");
                $("#edit_move_report_form").submit();
                location.hash = "#edit_move_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function edit_move_edit_init(id) {
   location.hash = "#edit_move";
   move_input_init('edit_move');
   update_forage('edit_move');
   $.ajax({
       type: "POST",
       url: "edit_move_edit_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var move = JSON.parse(data);
      $("#edit_move_edit_auto_id").val(id);
      create_date("edit_move_date", humanDate(move['move_date']));
      $("#edit_move_group").selectmenu();
      $("#edit_move_group").val(dc(move['animal_group']));
      $("#edit_move_group").selectmenu("refresh");
      update_subgroup('edit_move');
      $("#edit_move_subgroup").selectmenu();
      $("#edit_move_subgroup").val(dc(move['sub_group']));
      $("#edit_move_subgroup").selectmenu("refresh");
      $("#edit_move_move").selectmenu();
      $("#edit_move_move").val(move['move_to']);
      $("#edit_move_move").selectmenu("refresh");
      $("#edit_move_paddock").selectmenu();
      $("#edit_move_paddock").val(dc(move['paddock_id']));
      $("#edit_move_paddock").selectmenu("refresh");
      $("#edit_move_forage").selectmenu();
      $("#edit_move_forage").val(dc(move['forage']));
      $("#edit_move_forage").selectmenu("refresh");
      $("#edit_move_height").selectmenu();
      $("#edit_move_height").val(move['height']);
      $("#edit_move_height").selectmenu("refresh");
      $("#edit_move_density").selectmenu();
      $("#edit_move_density").val(move['density']);
      $("#edit_move_density").selectmenu("refresh");
      $("#edit_move_comments").val(dc(move['comments']));
   }
          },
          error: onError
      });
}

/*
function init_birth(id, any) {
   init_breed(id, any);
   init_mother(id, any);
   init_father(id, any);
   if (id == "birth_report") {
      init_id(id);
   }
}
*/

function init_all(id, any) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       url: "get_all.php?group=" + encodeURIComponent(group) + "&any=" + any,
       cache: false,
       async: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          $("#" + id + "_breed").html(content['breed']);
          $("#" + id + "_breed").selectmenu();
          $("#" + id + "_breed").selectmenu("refresh");
          $("#" + id + "_mother").html(content['mother']);
          $("#" + id + "_mother").selectmenu();
          $("#" + id + "_mother").selectmenu("refresh");
          $("#" + id + "_father").html(content['father']);
          $("#" + id + "_father").selectmenu();
          $("#" + id + "_father").selectmenu("refresh");
          $("#" + id + "_origin").html(content['origin']);
          $("#" + id + "_origin").selectmenu();
          $("#" + id + "_origin").selectmenu("refresh");
          $("#" + id + "_subgroup").html(content['sub_group']);
          $("#" + id + "_subgroup").selectmenu();
          $("#" + id + "_subgroup").selectmenu("refresh");
          if (id == "birth_report") {
             $("#" + id + "_id").html(content['id']);
             $("#" + id + "_id").selectmenu();
             $("#" + id + "_id").selectmenu("refresh");
          }
       },
       error: onError
   });
}

/*
function init_breed(id, any) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       url: "get_breed.php?group=" + encodeURIComponent(group),
       cache: false,
       //async: false,
       data: "",
       success: function (data, status) {
          var con = "";
          if (any) {
              con += "<option value='%'>ALL</option>";
          }
          con += data;
          $("#" + id + "_breed").html(con);
          $("#" + id + "_breed").selectmenu();
          $("#" + id + "_breed").selectmenu("refresh");
       },
       error: onError
   });
}

function init_mother(id, any) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       url: "get_mother.php?group=" + encodeURIComponent(group),
       cache: false,
       //async: false,
       data: "",
       success: function (data, status) {
          var con = "";
          if (any) {
              con += "<option value='%'>ALL</option>";
          }
          con += "<option value='N/A'>N/A</option>";
          con += data;
          $("#" + id + "_mother").html(con);
          $("#" + id + "_mother").selectmenu();
          $("#" + id + "_mother").selectmenu("refresh");
       },
       error: onError
   });
}

function init_father(id, any) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       url: "get_father.php?group=" + encodeURIComponent(group),
       cache: false,
       //async: false,
       data: "",
       success: function (data, status) {
          var con = "";
          if (any) {
              con += "<option value='%'>ALL</option>";
          }
          con += "<option value='N/A'>N/A</option>";
          con += data;
          $("#" + id + "_father").html(con);
          $("#" + id + "_father").selectmenu();
          $("#" + id + "_father").selectmenu("refresh");
       },
       error: onError
   });
}
*/


var meds;
var dose;

function init_vet(id, any, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_vet.php?any="+encodeURIComponent(any) + "&active=" +
            encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          $("#" + id + "_id").html(content['id']);
          $("#" + id + "_id").selectmenu();
          $("#" + id + "_id").selectmenu("refresh");
          $("#" + id + "_reason").html(content['reason']);
          $("#" + id + "_reason").selectmenu();
          $("#" + id + "_reason").selectmenu("refresh");
          meds = content['medication'];
          $("#vet_report_med").html("<option value='%'>ALL</option>" + meds);
          $("#vet_report_med").selectmenu();
          $("#vet_report_med").selectmenu("refresh");
          dose = content['dosage'];
       },
       error: onError
   });
}

function inSelect(select, value) {
   return 0 != $("#" + select + " option[value='" + value + "']").length;
}

var birth_input_initted = false;

function birth_input_init() {
   var fd = "";
   if (birth_input_initted) {
      fd = new FormData(document.getElementById("birth_input_form"));
   }
   create_date("birth_date", null);
   $("#name").val("");
   $("#birth_comments").val("");
   $("#birth_id").val("");
   clearForm("birth_file");
   init_group("birth", false, null);
   if (birth_input_initted) {
      var grp = fd.get("birth_group");
      var brd = fd.get("birth_breed");
      var subgrp = fd.get("birth_subgroup");
      var gen = fd.get("birth_gender");
      var mth = fd.get("birth_date_month");
      var dy = fd.get("birth_date_day");
      var yr = fd.get("birth_date_year");
      var orig = fd.get("birth_origin");
      var mom = fd.get("birth_mother");
      var dad = fd.get("birth_father");
      var color = fd.get("birth_color");
      $("#birth_date_month").val(mth);
      $("#birth_date_month").selectmenu("refresh");
      $("#birth_date_day").val(dy);
      $("#birth_date_day").selectmenu("refresh");
      $("#birth_date_year").val(yr);
      $("#birth_date_year").selectmenu("refresh");
      if (inSelect("birth_group", dc(grp))) {
         $("#birth_group").val(dc(grp));
         $("#birth_group").selectmenu("refresh");
         init_all("birth", false);
      }
      if (inSelect("birth_breed", dc(brd))) {
         $("#birth_breed").val(dc(brd));
         $("#birth_breed").selectmenu("refresh");
      }
      if (inSelect("birth_subgroup", dc(subgrp))) {
         $("#birth_subgroup").val(dc(subgrp));
         $("#birth_subgroup").selectmenu("refresh");
      }
      $("#birth_gender").val(gen);
      $("#birth_gender").selectmenu("refresh");
      if (inSelect("birth_origin", dc(orig))) {
         $("#birth_origin").val(dc(orig));
         $("#birth_origin").selectmenu("refresh");
      }
      if (inSelect("birth_mother", dc(mom))) {
         $("#birth_mother").val(dc(mom));
         $("#birth_mother").selectmenu("refresh");
      }
      if (inSelect("birth_father", dc(dad))) {
         $("#birth_father").val(dc(dad));
         $("#birth_father").selectmenu("refresh");
      }
   } else {
      birth_input_initted = true;
   }
}

function birth_report_init() {
   create_date("birth_date_from", null);
   create_date("birth_date_to", null);
   init_group("birth_report", true, null);
}

function vet_report_init() {
   create_date("vet_date_from", null);
   create_date("vet_date_to", null);
   init_vet("vet_report", true, true);
}

/*
function init_name(id) {
   var animal = $("#" + id + "_id").val();
   return $.ajax({
       type: "POST",
       url: "get_name.php?id=" + encodeURIComponent(animal),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_name").val(dc(data));
       },
       error: onError
   });
}
*/

var num_med_rows = 0;
var num_med_edit_rows = 0;

function vet_input_init() {
   create_date("vet_date", null);
   init_vet("vet", false, true);
//   init_name("vet");
   while (num_med_rows > 0) {
      remove_med_row();
   }
   $("#symptoms").val("");
   $("#careGiven").val("N/A");
   $("#vet_comments").val("N/A");
   animal_update('vet');
}

function add_med_row() {
   num_med_rows++;
   $("#num_med_rows").val(num_med_rows);
   var row = "<tr><td><select id='med_table_med_" + num_med_rows + 
             "' name='med_table_med_" + num_med_rows + 
             "' onchange='set_dose(" + num_med_rows + ", \"med_table\");'>" +
             meds + 
             "</select></td><td><select disabled id='med_table_dose_" + num_med_rows
           + "' name='med_table_dose_" + num_med_rows + "'>" + dose + 
             "</select></td><td><select id='med_table_unit_" + num_med_rows +
             "' name='med_table_unit_" + num_med_rows + "'>" +
             "<option value='mg'>mg</option>" +
             "<option value='cc'>cc</option>" +
             "<option value='oz'>oz</option>" +
             "<option value='each'>each</option>" +
             "<option value='pills'>pills</option></select></td><td>" +
             "<input type='number' step='0.01' min='0.01' id='med_table_given_" + num_med_rows +
             "' name='med_table_given_" + num_med_rows + "' style='width:5em'></td></tr>";
   $("#med_table").append(row);
   $("#med_table_med_" + num_med_rows).selectmenu();
   $("#med_table_med_" + num_med_rows).selectmenu("refresh");
   $("#med_table_dose_" + num_med_rows).selectmenu();
   $("#med_table_dose_" + num_med_rows).selectmenu("refresh");
   $("#med_table_unit_" + num_med_rows).selectmenu();
   $("#med_table_unit_" + num_med_rows).selectmenu("refresh");
}

function add_med_edit_row() {
   num_med_edit_rows++;
   $("#num_med_edit_rows").val(num_med_edit_rows);
   var row = "<tr><td><select id='med_edit_table_med_" + num_med_edit_rows + 
             "' name='med_edit_table_med_" + num_med_edit_rows + 
             "' onchange='set_dose(" + num_med_edit_rows + 
             ", \"med_edit_table\");'>" + meds + 
             "</select><td><select disabled id='med_edit_table_dose_" + num_med_edit_rows
           + "' name='med_edit_table_dose_" + num_med_edit_rows + "'>" + dose + 
             "</select></td><td><select id='med_edit_table_unit_" + num_med_edit_rows +
             "' name='med_edit_table_unit_" + num_med_edit_rows + "'>" +
             "<option value='mg'>mg</option>" +
             "<option value='cc'>cc</option>" +
             "<option value='oz'>oz</option>" +
             "<option value='each'>each</option>" +
             "<option value='pills'>pills</option></select></td><td>" +
             "<input type='number' step='0.01' min='0.01' id='med_edit_table_given_" + num_med_edit_rows +
             "' name='med_edit_table_given_" + num_med_edit_rows + 
             "' style='width:5em'></td></tr>";
   $("#med_edit_table").append(row);
   $("#med_edit_table_med_" + num_med_edit_rows).selectmenu();
   $("#med_edit_table_med_" + num_med_edit_rows).selectmenu("refresh");
   $("#med_edit_table_dose_" + num_med_edit_rows).selectmenu();
   $("#med_edit_table_dose_" + num_med_edit_rows).selectmenu("refresh");
   $("#med_edit_table_unit_" + num_med_edit_rows).selectmenu();
   $("#med_edit_table_unit_" + num_med_edit_rows).selectmenu("refresh");
}

function remove_med_row() {
   if (num_med_rows > 0) {
      num_med_rows--;
      $("#num_med_rows").val(num_med_rows);
      $("#med_table tr:last").remove();
   }
}

function remove_med_edit_row() {
   if (num_med_edit_rows > 0) {
      num_med_edit_rows--;
      $("#num_med_edit_rows").val(num_med_edit_rows);
      $("#med_edit_table tr:last").remove();
   }
}

function set_dose(row_num, id) {
   var ind = $("#" + id + "_med_" + row_num)[0].selectedIndex;
   $("#" + id + "_dose_" + row_num + " option")[ind].selected = true;
   $("#" + id + "_dose_" + row_num).selectmenu();
   $("#" + id + "_dose_" + row_num).selectmenu("refresh");
}

function move_input_init(id) {
   create_date(id + "_date", null);
   var con = "";
   for (var i = 1; i <= 48; i++) {
      con += "<option value='" + i + "'>" + i + "</option>";
   }
   $("#" + id + "_height").selectmenu();
   $("#" + id + "_height").html(con);
   $("#" + id + "_height").selectmenu("refresh");
   $.ajax({
       type: "POST",
       async: false,
       url: "get_move_init.php?active=true",
       cache: false,
       data: "",
       success: function (data, status) {
          var origVals = JSON.parse(data);
          var pad = origVals['paddock'];
          $("#" + id + "_paddock").selectmenu();
          $("#" + id + "_paddock").html(pad);
          $("#" + id + "_paddock").selectmenu("refresh");
          var group = origVals['group'];
          $("#" + id + "_group").selectmenu();
          $("#" + id + "_group").html(group);
          $("#" + id + "_group").selectmenu("refresh");
          var subgroup = origVals['subgroup'];
          $("#" + id + "_subgroup").selectmenu();
          $("#" + id + "_subgroup").html(subgroup);
          $("#" + id + "_subgroup").selectmenu("refresh");
          if (id == "move_input" || id == "edit_move") {
             update_move_paddock(id);
          }
       },
       error: onError
   });
/*
   $.ajax({
       type: "POST",
       async: false,
       url: "get_paddock.php?active=true",
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_paddock").selectmenu();
          $("#" + id + "_paddock").html(data);
          $("#" + id + "_paddock").selectmenu("refresh");
       },
       error: onError
   });
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_group.php",
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_group").selectmenu();
          $("#" + id + "_group").html(data);
          $("#" + id + "_group").selectmenu("refresh");
          update_subgroup(id, true);
       },
       error: onError
   });
*/
}

function update_move_paddock(id) {
   var group = $("#" + id + "_group").val();
   var subgroup = $("#" + id + "_subgroup").val();
   var mv = $("#" + id + "_move").val();
   $.ajax({
       type: "POST",
       async: false,
       url: "get_paddock_from.php?group=" + encodeURIComponent(group) + 
            "&subgroup=" + encodeURIComponent(subgroup) + "&move=" +
            encodeURIComponent(mv),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_paddock").selectmenu();
          $("#" + id + "_paddock").html(data);
          $("#" + id + "_paddock").selectmenu("refresh");
       },
       error: onError
   });
}

function move_report_init(id) {
   create_date(id + "_from", null);
   create_date(id + "_to", null);
   init_group(id, true, null);
   $.ajax({
       type: "POST",
       url: "get_paddock.php?active=true",
       cache: false,
       data: "",
       success: function (data, status) {
          var con = "<option value='%'>ALL</option>" + data;
          $("#" + id + "_paddock").selectmenu();
          $("#" + id + "_paddock").html(con);
          $("#" + id + "_paddock").selectmenu("refresh");
       },
       error: onError
   });
}

function showAlert(id, msg) {
   var win = document.getElementById(id);
   window.setTimeout(function() {closeAlert(id);}, 2000);
   win.innerHTML = "<center><h3>" + msg + "</center></h3>";
   win.style.display = "block";
   win.style.background = "white";
   win.style.border="thick solid black";
}

function closeAlert(id) {
   var win = document.getElementById(id);
   win.innerHTML = "&nbsp";
   win.style.border="none";
}

function onError(data, status) {
console.log(data);
    // handle an error
    location.hash = "#home";
    var con = "<h1>Error: database connection failed.</h1><p>" +
           "<form method='POST' data-ajax='false' action='login.php'><input type='submit' " +
           "class='ui-btn' style='width:100%' value='Log In Again'></form>";
    $("#home_div").html(con);
}         

function check_dup(name) {
   var dup = false;
   $.ajax({
       type: "POST",
       url: "check_name.php?name=" + encodeURIComponent(name),
       cache: false,
       async: false,
       data: "",
       success: function (data, status) {
          if (data == "true") {
             dup = true;
          }
       },
       error: onError
   });
   return dup;
}

function birth_input_validate() {
   var nm = $("#name").val();
   if (nm != "" && check_dup(nm) &&
       !confirm("Warning: duplicate name exists in database.")) {
      return false;
   }
   var id = $("#birth_id").val();
   if (id == "") {
      alert("Please enter a unique animal identifier.");
      return false;
   }
   var con = "Animal ID: " + id + "\n";
   
   var grp = $("#birth_group").val();
   if (grp == null) {
      alert("Please select an Animal Group.");
      return false;
   }
   con += "Animal Group: " + grp + "\n";
   var brd = $("#birth_breed").val();
   if (brd == null) {
      alert("Please select a Breed.");
      return false;
   }
   con += "Breed: " + brd + "\n";
   var sub = $("#birth_subgroup").val();
   if (sub == null) {
      alert("Please select a Subgroup.");
      return false;
   }
   con += "Subgroup: " + sub + "\n";
   var gen = $("#birth_gender").val();
   con += "Gender: " + gen + "\n";
   var dt = $("#birth_date_month").val() + "/" + $("#birth_date_day").val()
            + "/" + $("#birth_date_year").val();
   con += "Birth Date: " + dt + "\n";
   var org = $("#birth_origin").val();
   if (org == null) {
      alert("Please select an Origin.");
      return false;
   }
   con += "Origin: " + org + "\n";
   var mom = $("#birth_mother").val();
   con += "Mother: " + mom + "\n";
   var dad = $("#birth_father").val();
   con += "Father: " + dad + "\n";
   con += "Name: " + nm + "\n";
   var mark = $("#mark").val();
   con += "Markings: " + mark + "\n";
   var com = $("#birth_comments").val();
   con += "Comments: " + com + "\n";
   var pica = document.getElementById("birth_file").files;
   if (pica.length > 0) {
      var pic = pica[0].name;
      var pos = pic.lastIndexOf(".");
      var ext = pic.substring(pos + 1, pic.length).toLowerCase();
      if (ext != "gif" && ext != "png" && ext != "jpg" && ext != "jpeg") {
         alert("Invalid image type: only gif, png, jpg and jpeg allowed.");
         return false;
      }
      con += "Picture: " + pic + "\n";
   }
   return confirm("Confirm Entry:\n"+con);
}

function vet_input_validate() {
   var dt = $("#vet_date_month").val() + "/" + $("#vet_date_day").val()
            + "/" + $("#vet_date_year").val();
   var con = "Date: " + dt + "\n";
   var id = $("#vet_id").val();
   if (id == null) {
      alert("Please select an Animal ID.");
      return false;
   }
   con += "Animal ID: " + id + "\n";
   var name = $("#vet_name").val();
   con += "Name: " + name + "\n";
   var reason = $("#vet_reason").val();
   if (reason == null) {
      alert("Please select a Reason.");
      return false;
   }
   con += "Reason for Care: " + reason + "\n";
   var sym = $("#symptoms").val();
   con += "Symptoms: " + sym + "\n";
   var temp = $("#temperature").val();
   con += "Temperature: " + temp + "\n";
   var care = $("#careGiven").val();
   con += "Care Given: " + care + "\n";

   var num = $("#num_med_rows").val();
   con += "Medication Given: ";
   if (num == 0) {
       con += "None\n";
   } else {
      con += "\n\n";
      for (var i = 1; i <= num; i++) {
         var med = $("#med_table_med_" + i).val();
         var dose = $("#med_table_dose_" + i).val();
         var unit = $("#med_table_unit_" + i).val();
         var given = $("#med_table_given_" + i).val();
         if (given == "") {
            alert("Enter units given for " + med + " on line " + i + ".");
            return false;
         } else if (!isFinite(given) || given <= 0) {
            alert("Enter valid units given for " + med + " on line " + i + ".");
            return false;
         }
         con += "Medication: " + med + "\n";
         con += "Dosage: " + dose + "\n";
         con += "Unit: " + unit + "\n";
         con += "Units Given: " + given + "\n\n";
      }
   }

   var weight = $("#vet_weight").val();
   con += "Estimated Weight: " + weight + "\n";
   var adv = $("#vet_advisor").val();
   con += "Vet/Advisor: " + adv + "\n";
   var cont = $("#vet_contact").val();
   con += "Contact: " + cont + "\n";
   var ast = $("#vet_assist").val();
   con += "Assistants: " + ast + "\n";
   var com = $("#vet_comments").val();
   con += "Comments: " + com + "\n";

   return confirm("Confirm Entry:\n"+con);
}

function egg_input_validate() {
   var dt = $("#egg_input_date_month").val() + "/" + $("#egg_input_date_day").val()
            + "/" + $("#egg_input_date_year").val();
   var con = "Date: " + dt + "\n";
   var num = parseInt($("#egg_input_amt").val());
   if (num < 1) {
      alert("Invalid number of eggs collected.");
      return false;
   }
   con += "Number of Eggs Collected: " + num + "\n";
   var com = $("#egg_input_comments").val();
   con += "Comments: " + com + "\n";

   return confirm("Confirm Entry:\n"+con);
}

function forage_validate() {
   var frg = $("#add_forage_forage").val();
   var dens = $("#add_forage_density").val();
   if (frg == "") {
      alert("Please enter a forage.");
      return false;
   }
   if (dens == "") {
      alert("Density must be greater than 0.");
      return false;
   }
   var con = "Forage: " + frg + "\nDensity: " + dens + " lbs/acre-inch\n";

   return confirm("Confirm Entry:\n"+con);
}

function paddock_validate() {
   var id = $("#add_paddock_id").val();
   var size = $("#add_paddock_size").val();
   var frg = $("#add_paddock_forage").val();
   if (id == "") {
      alert("Please enter a Paddock ID.");
      return false;
   }
   if (frg == null) {
      alert("Please select a forage.");
      return false;
   }
   if (size == "") {
      alert("Size must be greater than 0.");
      return false;
   }
   var con = "Paddock ID: " + id + "\nSize: " + size + " (acres)\nForage: " +
             frg + "\n";

   return confirm("Confirm Entry:\n"+con);
}

function move_input_validate() {
   var dt = $("#move_input_date_month").val() + "/" + $("#move_input_date_day").val()
            + "/" + $("#move_input_date_year").val();
   var grp = $("#move_input_group").val();
   if (grp == null) {
      alert("Please select an Animal Group.");
      return false;
   }
   var subgrp = $("#move_input_subgroup").val();
   if (subgrp == null) {
      alert("Please select a Subgroup.");
      return false;
   }
   var mv = $("#move_input_move").find(":selected").text();
   var pad = $("#move_input_paddock").val();
   if (pad == null) {
      alert("Please select a Paddock ID.");
      return false;
   }
   var height = $("#move_input_height").val();
   var dens = $("#move_input_density").find(":selected").text();
   var com = $("#move_input_comments").val();

   var con = "Date: " + dt + "\nAnimal Group: " + grp + "\nSubgroup: " +
             subgrp + "\nMove " + mv + " Paddock\nPaddock ID: " + pad +
             "\nForage Height: " + height + " (inches)\nForage Density: " +
             dens + "\nComments: " + com;

   return confirm("Confirm Entry:\n"+con);
}

function clearForm(id) {
   var con = '<label for="' + id + '">Picture (optional):</label>';
   con += '<input type="file" name="' + id + '" id="' + id + '">';
   $("#" + id + "_div").html(con);
}

function clearEdit(id) {
   $("#" + id).val("");
}

function humanDate(dt) {
   var dtArr = dt.split("-");
   return dtArr[1] + "/" + dtArr[2] + "/" + dtArr[0];
}

function birth_edit_init(id) {
   clearForm("birth_edit_file");
   var formData = new FormData();
   formData.append("id", id);
   $.ajax({
       type: "POST",
       url: "birth_edit_data.php",
       cache: false,
       data: formData,
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var origVals = JSON.parse(data);
      create_date("birth_edit_date", humanDate(origVals['birthdate']));
      init_group("birth_edit", false, dc(origVals['animal_group']));
      $("#birth_edit_orig_id").val(dc(origVals['animal_id']));
      $("#birth_edit_auto_id").val(id);
      $("#birth_edit_orig_file").val(dc(origVals['filename']));
      $("#birth_edit_id").val(dc(origVals['animal_id']));
      $("#birth_edit_breed").val(dc(origVals['breed']));
      $("#birth_edit_breed").selectmenu("refresh");
      $("#birth_edit_subgroup").val(dc(origVals['sub_group']));
      $("#birth_edit_subgroup").selectmenu("refresh");
      $("#birth_edit_gender").val(origVals['gender']);
      $("#birth_edit_gender").selectmenu("refresh");
      $("#birth_edit_origin").val(dc(origVals['origin']));
      $("#birth_edit_origin").selectmenu("refresh");
      $("#birth_edit_mother").val(dc(origVals['mother']));
      $("#birth_edit_mother").selectmenu("refresh");
      $("#birth_edit_father").val(dc(origVals['father']));
      $("#birth_edit_father").selectmenu("refresh");
      $("#name_edit").val(dc(origVals['name']));
      $("#mark_edit").val(dc(origVals['markings']));
      $("#birth_edit_alive").val(origVals['alive']);
      $("#birth_edit_alive").selectmenu("refresh");
      $("#birth_edit_comments").val(dc(origVals['comments']));
      var file = dc(origVals['filename']);
      if (file == "") {
         file = "None";
      }
      $("#birth_edit_current_picture").val(file);
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function htmlspecialchars_decode (string, quoteStyle) { 
  // eslint-disable-line camelcase
  //       discuss at: http://locutus.io/php/htmlspecialchars_decode/
  //      original by: Mirek Slugen
  //      improved by: Kevin van Zonneveld (http://kvz.io)
  //      bugfixed by: Mateusz "loonquawl" Zalega
  //      bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //         input by: ReverseSyntax
  //         input by: Slawomir Kaniecki
  //         input by: Scott Cariss
  //         input by: Francois
  //         input by: Ratheous
  //         input by: Mailfaker (http://www.weedem.fr/)
  //       revised by: Kevin van Zonneveld (http://kvz.io)
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //        example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES')
  //        returns 1: '<p>this -> &quot;</p>'
  //        example 2: htmlspecialchars_decode("&amp;quot;")
  //        returns 2: '&quot;'

  var optTemp = 0
  var i = 0
  var noquotes = false

  if (typeof quoteStyle === 'undefined') {
    quoteStyle = 2
  }
  string = string.toString()
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  }
  if (quoteStyle === 0) {
    noquotes = true
  }
  if (typeof quoteStyle !== 'number') {
    // Allow for a single string or an array of string flags
    quoteStyle = [].concat(quoteStyle)
    for (i = 0; i < quoteStyle.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quoteStyle[i]] === 0) {
        noquotes = true
      } else if (OPTS[quoteStyle[i]]) {
        optTemp = optTemp | OPTS[quoteStyle[i]]
      }
    }
    quoteStyle = optTemp
  }
  if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
    // PHP doesn't currently escape if more than one 0, but it should:
    string = string.replace(/&#0*39;/g, "'")
    // This would also be useful here, but not a part of PHP:
    // string = string.replace(/&apos;|&#x0*27;/g, "'");
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"')
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&')

  return string
}

function dc(str) {
   var undef;
   if (str == "" || str === undef) {
      return "";
   } else {
      return htmlspecialchars_decode(str, 3);
   }
}

function vet_edit_init(id) {
   var formData = new FormData();
   formData.append("id", id);
   $.ajax({
       type: "POST",
       url: "vet_edit_data.php",
       cache: false,
       data: formData,
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var origVals = JSON.parse(data);
      var vet = origVals['vet'];
      var meds_given = origVals['meds_given'];
      $("#vet_edit_auto_id").val(id);
      create_date("vet_edit_date", humanDate(vet['care_date']));
      init_vet("vet_edit", false, false);
      $("#vet_edit_id").selectmenu();
      $("#vet_edit_id").val(dc(vet['animal_id']));
      $("#vet_edit_id").selectmenu("refresh");
      $("#vet_edit_reason").selectmenu();
      $("#vet_edit_reason").val(dc(vet['reason']));
      $("#vet_edit_reason").selectmenu("refresh");
      $("#symptoms_edit").val(dc(vet['symptoms']));
      $("#temperature_edit").selectmenu();
      $("#temperature_edit").val(vet['temperature']);
      $("#temperature_edit").selectmenu("refresh");
      $("#care_edit").val(dc(vet['care']));
      $("#vet_edit_weight").val(dc(vet['weight']));
      $("#vet_edit_advisor").val(dc(vet['vet']));
      $("#vet_edit_contact").selectmenu();
      $("#vet_edit_contact").val(dc(vet['contact']));
      $("#vet_edit_contact").selectmenu("refresh");
      $("#vet_edit_assist").val(dc(vet['assistants']));
      $("#vet_edit_comments").val(dc(vet['comments']));
      while (num_med_edit_rows > 0) {
         remove_med_edit_row();
      }
      if (meds_given != "") {
         for (var i = 0; i < meds_given.length; i++) {
             add_med_edit_row();
             var row = i + 1;
             $("#med_edit_table_med_" + row).selectmenu();
             $("#med_edit_table_med_" + row).val(dc(meds_given[i]['medication']));
             $("#med_edit_table_med_" + row).selectmenu("refresh");
             set_dose(row, "med_edit_table");
             $("#med_edit_table_unit_" + row).selectmenu();
             $("#med_edit_table_unit_" + row).val(dc(meds_given[i]['units']));
             $("#med_edit_table_unit_" + row).selectmenu("refresh");
             $("#med_edit_table_given_" + row).val(
                dc(meds_given[i]['units_given']));
         }
      }
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function birth_delete(id) {
   if (confirm("Warning: only delete animal records that have been entered " +
               "in error.  Deleting this record will leave the database " +
               "in an inconsistent state if this animal is the parent of " +
               "another animal or any records referring to this animal have " +
               "already been entered.  Do you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_animal.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Animal record successfully deleted.");
                location.hash = "#birth_report";
                $("#birth_report_form").submit();
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function vet_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_vet.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Vet record successfully deleted.");
                location.hash = "#vet_report";
                $("#vet_report_form").submit();
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function add_reason(hash, id) {
   location.hash = "#add_reason";
   returnHash = hash;
   reasonId = id;
}

function update_reason(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_reason.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_reason").html(data);
          $("#" + id + "_reason").selectmenu();
          $("#" + id + "_reason").selectmenu("refresh");
       },
       error: onError
   });
}

var medicationId = "";

function add_medication(hash, id) {
   location.hash = "#add_medication";
   returnHash = hash;
   medicationId = id;
}

function update_medication(id) {
   return $.ajax({
       type: "POST",
       url: "get_vet.php?any=false",
       cache: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          meds = content['medication'];
          dose = content['dosage'];
          var rows = 0;
          if (id == "med_table") {
             rows = num_med_rows;
          } else {
             rows = num_med_edit_rows;
          }
          if (rows > 0) {
             $("#" + id + "_med_" + rows).html(meds);
             $("#" + id + "_med_" + rows).selectmenu();
             $("#" + id + "_med_" + rows).selectmenu("refresh");
             $("#" + id + "_dose_" + rows).html(dose);
             $("#" + id + "_dose_" + rows).selectmenu();
             $("#" + id + "_dose_" + rows).selectmenu("refresh");
          }
       },
       error: onError
   });
}

var originId = "";

function add_origin(hash, id) {
   location.hash = "#add_origin";
   returnHash = hash;
   originId = id;
}

function update_origin(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_origin.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_origin").html(data);
          $("#" + id + "_origin").selectmenu();
          $("#" + id + "_origin").selectmenu("refresh");
       },
       error: onError
   });
}

function init_origin() {
   update_origin('edit_origin', false);
   update_origin_active('edit_origin');
}

var breedId = "";

function add_breed(hash, id) {
   var group = $("#" + id + "_group").val();
   init_group('add_breed', false, group);
   location.hash = "#add_breed";
   returnHash = hash;
   breedId = id;
}

function update_breed(id, active) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_breed.php?group=" + encodeURIComponent(group) + "&active=" + 
            encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_breed").html(data);
          $("#" + id + "_breed").selectmenu();
          $("#" + id + "_breed").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_breed() {
   return $.ajax({
       type: "POST",
       url: "get_group.php",
       cache: false,
       data: "",
       success: function (data, status) {
          $("#edit_breed_group").html(data);
          $("#edit_breed_group").selectmenu();
          $("#edit_breed_group").selectmenu("refresh");
          update_breed("edit_breed", false);
          update_breed_active("edit_breed");
       },
       error: onError
   });
}

function update_breed_active(id) {
   var group = $("#" + id + "_group").val();
   var breed = $("#" + id + "_breed").val();
   return $.ajax({
       type: "POST",
       url: "get_breed_active.php?group=" + encodeURIComponent(group) + "&breed=" + 
            encodeURIComponent(breed),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

var subgroupId = "";

function add_subgroup(hash, id) {
   var group = $("#" + id + "_group").val();
   init_group('add_subgroup', false, group);
   location.hash = "#add_subgroup";
   returnHash = hash;
   subgroupId = id;
}

function update_subgroup(id, active) {
   var group = $("#" + id + "_group").val();
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_subgroup.php?group=" + encodeURIComponent(group) + 
            "&active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_subgroup").html(data);
          $("#" + id + "_subgroup").selectmenu();
          $("#" + id + "_subgroup").selectmenu("refresh");
          if (id == "move_input" || id == "edit_move") {
             update_move_paddock(id);
          }
       },
       error: onError
   });
}

function init_edit_subgroup() {
   return $.ajax({
       type: "POST",
       url: "get_group.php",
       cache: false,
       data: "",
       success: function (data, status) {
          $("#edit_subgroup_group").html(data);
          $("#edit_subgroup_group").selectmenu();
          $("#edit_subgroup_group").selectmenu("refresh");
          update_subgroup("edit_subgroup", false);
          update_subgroup_active("edit_subgroup");
       },
       error: onError
   });
}

function update_subgroup_active(id) {
   var group = $("#" + id + "_group").val();
   var subgroup = $("#" + id + "_subgroup").val();
   return $.ajax({
       type: "POST",
       url: "get_subgroup_active.php?group=" + encodeURIComponent(group) + 
            "&subgroup=" + encodeURIComponent(subgroup),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

var destId = "";

function add_dest(hash, id) {
   location.hash = "#add_dest";
   returnHash = hash;
   destId = id;
}

function update_dest(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_dest.php?active=" + encodeURIComponent(active) + "&all=" +
            encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_dest").html(data);
          $("#" + id + "_dest").selectmenu();
          $("#" + id + "_dest").selectmenu("refresh");
       },
       error: onError
   });
}

function update_dest_sale_active(id) {
   var dest = $("#" + id + "_dest").val();
   return $.ajax({
       type: "POST",
       url: "get_dest_sale_active.php?dest=" + encodeURIComponent(dest),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_sale_dest() {
   update_dest('edit_dest_sale', false, false);
   update_dest_sale_active('edit_dest_sale');
}
   
function update_reason_active(id) {
   var reason = $("#" + id + "_reason").val();
   return $.ajax({
       type: "POST",
       url: "get_reason_active.php?reason=" + encodeURIComponent(reason),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_reason() {
   update_reason("edit_reason", false);
   update_reason_active("edit_reason");
}

function update_origin_active(id) {
   var origin = $("#" + id + "_origin").val();
   return $.ajax({
       type: "POST",
       url: "get_origin_active.php?origin=" + encodeURIComponent(origin),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function update_med(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_med.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_med").html(data);
          $("#" + id + "_med").selectmenu();
          $("#" + id + "_med").selectmenu("refresh");
       },
       error: onError
   });
}

function update_edit_med() {
   var med = $("#edit_med_med").val();
   return $.ajax({
       type: "POST",
       url: "med_edit_data.php?med=" + encodeURIComponent(med),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_med_dose").val(dc(origVals['dosage']));
             $("#edit_med_active").val(origVals['active']);
             $("#edit_med_active").selectmenu();
             $("#edit_med_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function init_edit_med() {
   update_med("edit_med", false);
   update_edit_med();
}

function update_wormer(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_wormer.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_wormer").html(data);
          $("#" + id + "_wormer").selectmenu();
          $("#" + id + "_wormer").selectmenu("refresh");
       },
       error: onError
   });
}

function update_edit_wormer() {
   var wormer = $("#edit_wormer_wormer").val();
   return $.ajax({
       type: "POST",
       url: "wormer_edit_data.php?wormer=" + encodeURIComponent(wormer),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_wormer_active").val(origVals['active']);
             $("#edit_wormer_active").selectmenu();
             $("#edit_wormer_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function init_edit_wormer() {
   update_wormer("edit_wormer", false);
   update_edit_wormer();
}

function update_all_subgroups() {

     var numrows = parseInt($("#update_subgroups_rows").val());
     var fd = new FormData();
     fd.append("update_subgroups_rows", numrows);
     for (var i = 1; i <= numrows; i++) {
        var autoID = parseInt($("#sg_edit_id" + i).val());
        var sg = $("#sg_edit" + i).val();
        fd.append("sg_edit_id" + i, autoID);
        fd.append("sg_edit" + i, sg);
     }
     $.ajax({
        type: "POST",
        url: "update_all_subgroups.php",
        cache: false,
        data: fd,
        success: 
function(data, status) {
   if (data == "success!") {
     alert("Subgroups successfully updated.");
     location.hash = "#edit_subgroups";
     $("#edit_subgroups_form").submit();
   } else {
      alert(data);
   }
},
        contentType: false,
        processData: false,
        error: onError
    });
    return false;
}

function change_subgroup(id, start, stop) {
   var subgrp = $("#" + id).val();
   for (var i = start; i <= stop; i++) {
      $("#sg_edit" + i).val(dc(subgrp));
      $("#sg_edit" + i).selectmenu("refresh");
   }
} 

var paddockId = "";

function add_forage(hash, id) {
   location.hash = "#add_forage";
   returnHash = hash;
   paddockId = id;
}

function edit_user_init() {
   return $.ajax({
       type: "POST",
       url: "user_edit_data.php",
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_user_name").selectmenu();
             $("#edit_user_name").html(origVals['usernames']);
             $("#edit_user_name").selectmenu("refresh");
             $("#edit_user_admin").selectmenu();
             $("#edit_user_admin").val(origVals['user']['admin']);
             $("#edit_user_admin").selectmenu("refresh");
             $("#edit_user_active").selectmenu();
             $("#edit_user_active").val(origVals['user']['active']);
             $("#edit_user_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function update_user() {
   var user = $("#edit_user_name").val();
   return $.ajax({
       type: "POST",
       url: "get_user_data.php?user=" + encodeURIComponent(user),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             var origVals = JSON.parse(data);
             $("#edit_user_admin").selectmenu();
             $("#edit_user_admin").val(origVals['admin']);
             $("#edit_user_admin").selectmenu("refresh");
             $("#edit_user_active").selectmenu();
             $("#edit_user_active").val(origVals['active']);
             $("#edit_user_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function update_animal_id(id, alive, orig) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_animal_id.php?alive=" + encodeURIComponent(alive),
       cache: false,
       data: "",
       success: function (data, status) {
          var con = "";
          if (orig != null) {
             con = "<option value='" + orig + "'>" + orig + "</option>";
          }
          $("#" + id + "_id").html(con + data);
          $("#" + id + "_id").selectmenu();
          $("#" + id + "_id").selectmenu("refresh");
       },
       error: onError
   });
}

function init_animal_record() {
   update_animal_id('animal_record', false, null);
   show_animal_record();
}

function show_animal_record() {
   var id = $("#animal_record_id").val();
   return $.ajax({
       type: "POST",
       url: "get_animal_all.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#animal_record_div").html(data);
       },
       error: onError
   });
}

function init_notes(id) {
   check_dup("foo"); // only to check database connection
   create_date(id + "_date", null);
}

function notes_validate() {
   var dt = $("#notes_date_month").val() + "/" + $("#notes_date_day").val()
            + "/" + $("#notes_date_year").val();
   var con = "Date: " + dt + "\n";
   var com = $("#notes_note").val();
   if (com == "") {
      alert("Please enter a note.");
      return false;
   }
   con += "Note: " + com + "\n";
   var pica = document.getElementById("notes_file").files;
   if (pica.length > 0) {
      var pic = pica[0].name;
      var pos = pic.lastIndexOf(".");
      var ext = pic.substring(pos + 1, pic.length).toLowerCase();
      if (ext != "gif" && ext != "png" && ext != "jpg" && ext != "jpeg") {
         alert("Invalid image type: only gif, png, jpg and jpeg allowed.");
         return false;
      }
      con += "Picture: " + pic + "\n";
   }

   return confirm("Confirm Entry:\n"+con);
}

function notes_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_note.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Note successfully deleted.");
                location.hash = "#notes_report";
                $("#notes_report_form").submit();
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function notes_edit_init(id) {
   clearForm("notes_edit_file");
   var formData = new FormData();
   formData.append("id", id);
   $.ajax({
       type: "POST",
       url: "notes_edit_data.php",
       cache: false,
       data: formData,
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      $("#notes_edit_auto_id").val(id);
      var origVals = JSON.parse(data);
      $("#notes_edit_orig_file").val(dc(origVals['filename']));
      create_date("notes_edit_date", humanDate(origVals['note_date']));
      $("#notes_edit_note").val(dc(origVals['note']));
      var file = dc(origVals['filename']);
      if (file == "") {
         file = "None";
      }
      $("#notes_edit_current_picture").val(file);
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

var sale_input_initted = false;

function init_sale_input(id) {
   var fd = "";
   if (sale_input_initted) {
      fd = new FormData(document.getElementById("sale_input_form"));
   }
   create_date(id + "_date", null);
   update_animal_id(id, true, null);
   animal_update(id);
   update_dest(id, true, false);
   if (sale_input_initted) {
      var mth = fd.get("sale_input_date_month");
      var dy = fd.get("sale_input_date_day");
      var yr = fd.get("sale_input_date_year");
      var tag = fd.get("sale_input_tag");
      var dest = fd.get("sale_input_dest");
      var est = fd.get("sale_input_estimated");
      var price = fd.get("sale_input_price");
      var fee = fd.get("sale_input_fee");
      var an_id = fd.get("sale_input_id");
      var weight = fd.get("sale_input_weight");
      $("#sale_input_date_month").val(mth);
      $("#sale_input_date_month").selectmenu("refresh");
      $("#sale_input_date_day").val(dy);
      $("#sale_input_date_day").selectmenu("refresh");
      $("#sale_input_date_year").val(yr);
      $("#sale_input_date_year").selectmenu("refresh");
      if (inSelect("sale_input_id", dc(an_id))) {
         $("#sale_input_id").val(dc(an_id));
         $("#sale_input_id").selectmenu("refresh");
         // update_animal_id(id, true, null);
         animal_update(id);
      }
      $("#sale_input_tag").val(dc(tag));
      if (inSelect("sale_input_dest", dc(dest))) {
         $("#sale_input_dest").val(dc(dest));
         $("#sale_input_dest").selectmenu("refresh");
      }
      $("#sale_input_estimated").val(est);
      $("#sale_input_price").val(price);
      $("#sale_input_fee").val(fee);
      $("#sale_input_weight").val(weight);
   } else {
      sale_input_initted = true;
   }
}

function update_total_price(id) {
   var price = $("#" + id + "_price").val();
   var wt = $("#" + id + "_weight").val();
   if (price != "" && wt != "") {
      var tot = parseFloat(price) * parseFloat(wt);
      $("#" + id + "_total_price").val(tot.toFixed(2));
      update_net_price(id);
   }
}

function update_net_price(id) {
   var price = $("#" + id + "_total_price").val();
   var fee = $("#" + id + "_fee").val();
   if (price != "" && fee != "") {
      var tot = parseFloat(price) - parseFloat(fee);
      $("#" + id + "_net_price").val(tot.toFixed(2));
   }
}

function sale_input_validate() {
   var dt = $("#sale_input_date_month").val() + "/" + $("#sale_input_date_day").val()
            + "/" + $("#sale_input_date_year").val();
   var id = $("#sale_input_id").val();
   if (id == null) {
      alert("Please select an Animal ID.");
      return false;
   }
   var tag = $("#sale_input_tag").val();
   if (tag == "") {
      alert("Please enter a sales tag.");
      return false;
   }
   var dest = $("#sale_input_dest").val();
   if (dest == null) {
      alert("Please select a Destination.");
      return false;
   }
   var weight = $("#sale_input_weight").val();
   if (weight == "" || parseFloat(weight) <= 0) {
      alert("Please enter a valid weight.");
      return false;
   }
   var est = $("#sale_input_estimated").val();
   var price = $("#sale_input_price").val();
   if (price == "" || parseFloat(price) <= 0) {
      alert("Please enter a valid price per lb.");
      return false;
   }
   var fee = $("#sale_input_fee").val();
   if (fee == "" || parseFloat(fee) < 0) {
      alert("Please enter a valid sales & hauling fee.");
      return false;
   }
   var com = $("#sale_input_comments").val();

   var con = "Date: " + dt + "\nAnimal ID: " + id + "\nSales Tag: " +
             tag + "\nDestination: " + dest +
             "\nWeight: " + weight + " (lbs.) (" + est + ")" +
             "\nPrice per lb. ($): " + price + "\nHauling & Fees: " +
             fee + "\nComments: " + com;

   return confirm("Confirm Entry:\n"+con);
}

function init_sale_report() {
   create_date("sale_report_from", null);
   create_date("sale_report_to", null);
   init_group("sale_report", true, null);
   update_dest("sale_report", true, true);
}

function sale_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_sale.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Sale record successfully deleted.");
                update_animal_id('sale_input', true, null);
                update_animal_id('slay_input', true, null);
                update_animal_id('other_input', true, null);
                $("#sale_report_form").submit();
                location.hash = "#sale_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function sale_edit_init(id) {
   location.hash = "#sale_edit";
   update_dest('sale_edit', true, false);
   $.ajax({
       type: "POST",
       url: "edit_sale_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var sale = JSON.parse(data);
      $("#sale_edit_auto_id").val(id);
      $("#sale_edit_orig_id").val(dc(sale['animal_id']));
      create_date("sale_edit_date", humanDate(sale['sale_date']));
      update_animal_id('sale_edit', true, sale['animal_id']);
      $("#sale_edit_id").selectmenu();
      $("#sale_edit_id").val(dc(sale['animal_id']));
      $("#sale_edit_id").selectmenu("refresh");
      $("#sale_edit_tag").val(dc(sale['sale_tag']));
      $("#sale_edit_dest").selectmenu();
      $("#sale_edit_dest").val(dc(sale['destination']));
      $("#sale_edit_dest").selectmenu("refresh");
      $("#sale_edit_weight").val(sale['weight']);
      $("#sale_edit_estimated").selectmenu();
      $("#sale_edit_estimated").val(sale['estimated']);
      $("#sale_edit_estimated").selectmenu("refresh");
      $("#sale_edit_price").val(sale['price_lb']);
      $("#sale_edit_fee").val(sale['fees']);
      $("#sale_edit_comments").val(dc(sale['comments']));
   }
          },
          error: onError
      });
}

var slay_input_initted = false;

function init_slay_input(id) {
   var fd = "";
   if (slay_input_initted) {
      fd = new FormData(document.getElementById("slay_input_form"));
   }
   create_date(id + "_date", null);
   update_animal_id(id, true, null);
   animal_update(id);
   update_slayhouse(id, true, false);
   if (slay_input_initted) {
      var mth = fd.get("slay_input_date_month");
      var dy = fd.get("slay_input_date_day");
      var yr = fd.get("slay_input_date_year");
      var an_id = fd.get("slay_input_id");
      var tag = fd.get("slay_input_tag");
      var weight = fd.get("slay_input_weight");
      var est = fd.get("slay_input_estimated");
      var house = fd.get("slay_input_house");
      var hauler = fd.get("slay_input_hauler");
      var haul_equip = fd.get("slay_input_haul_equip");
      var fee = fd.get("slay_input_fee");

      $("#slay_input_date_month").val(mth);
      $("#slay_input_date_month").selectmenu("refresh");
      $("#slay_input_date_day").val(dy);
      $("#slay_input_date_day").selectmenu("refresh");
      $("#slay_input_date_year").val(yr);
      $("#slay_input_date_year").selectmenu("refresh");
      if (inSelect("slay_input_id", dc(an_id))) {
         $("#slay_input_id").val(dc(an_id));
         $("#slay_input_id").selectmenu("refresh");
         animal_update(id);
      }
      $("#slay_input_tag").val(dc(tag));
      $("#slay_input_weight").val(weight);
      $("#slay_input_estimated").val(est);
      $("#slay_input_estimated").selectmenu("refresh");
      $("#slay_input_house").val(dc(house));
      $("#slay_input_house").selectmenu("refresh");
      $("#slay_input_hauler").val(dc(hauler));
      $("#slay_input_haul_equip").val(dc(haul_equip));
      $("#slay_input_fee").val(fee);
   } else {
      slay_input_initted = true;
   }
}

var slayhouseId = "";

function add_slayhouse(hash, id) {
   location.hash = "#add_slayhouse";
   returnHash = hash;
   slayhouseId = id;
}

function update_slayhouse(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_slayhouse.php?active=" + encodeURIComponent(active) + "&all=" +
            encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_house").html(data);
          $("#" + id + "_house").selectmenu();
          $("#" + id + "_house").selectmenu("refresh");
       },
       error: onError
   });
}

function update_slayhouse_active(id) {
   var house = $("#" + id + "_house").val();
   return $.ajax({
       type: "POST",
       url: "get_slayhouse_active.php?house=" + encodeURIComponent(house),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function slay_input_validate() {
   var dt = $("#slay_input_date_month").val() + "/" + $("#slay_input_date_day").val()
            + "/" + $("#slay_input_date_year").val();
   var id = $("#slay_input_id").val();
   if (id == null) {
      alert("Please select an Animal ID.");
      return false;
   }
   var tag = $("#slay_input_tag").val();
   if (tag == "") {
      alert("Please enter a sales tag.");
      return false;
   }
   var weight = $("#slay_input_weight").val();
   if (weight == "" || parseFloat(weight) <= 0) {
      alert("Please enter a valid weight.");
      return false;
   }
   var est = $("#slay_input_estimated").val();
   var house = $("#slay_input_house").val();
   if (house == null) {
      alert("Please select a Slaughter House.");
      return false;
   }
   var haul = $("#slay_input_hauler").val();
   if (haul == "") {
      alert("Please enter a hauler.");
      return false;
   }
   var haul_equip = $("#slay_input_haul_equip").val();
   if (haul_equip == "") {
      alert("Please enter hauling equipment.");
      return false;
   }
   var fee = $("#slay_input_fee").val();
   if (fee == "" || parseFloat(fee) < 0) {
      alert("Please enter a valid fee.");
      return false;
   }
   var com = $("#slay_input_comments").val();

   var con = "Date: " + dt + "\nAnimal ID: " + id + "\nSales Tag: " +
             tag + "\nWeight: " + weight + " (lbs.) (" + est + ")" +
             "\nSlaughter House: " + house + "\nHauler: " + haul +
             "\nHauling Equipment: " + haul_equip + "\nFees: $" + fee +
             "\nComments: " + com;

   return confirm("Confirm Entry:\n"+con);
}

function init_slay_report() {
   create_date("slay_report_from", null);
   create_date("slay_report_to", null);
   init_group("slay_report", true, null);
   update_slayhouse("slay_report", true, true);
}

function slay_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_slay.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Slaughter record successfully deleted.");
                update_animal_id('sale_input', true, null);
                update_animal_id('slay_input', true, null);
                update_animal_id('other_input', true, null);
                $("#slay_report_form").submit();
                location.hash = "#slay_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function slay_edit_init(id) {
   location.hash = "#slay_edit";
   update_slayhouse('slay_edit', true, false);
   $.ajax({
       type: "POST",
       url: "edit_slay_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var slay = JSON.parse(data);
      $("#slay_edit_auto_id").val(id);
      $("#slay_edit_orig_id").val(dc(slay['animal_id']));
      create_date("slay_edit_date", humanDate(slay['slay_date']));
      update_animal_id('slay_edit', true, slay['animal_id']);
      $("#slay_edit_id").selectmenu();
      $("#slay_edit_id").val(dc(slay['animal_id']));
      $("#slay_edit_id").selectmenu("refresh");
      $("#slay_edit_tag").val(dc(slay['sale_tag']));
      $("#slay_edit_house").selectmenu();
      $("#slay_edit_house").val(dc(slay['slay_house']));
      $("#slay_edit_house").selectmenu("refresh");
      $("#slay_edit_weight").val(slay['weight']);
      $("#slay_edit_estimated").selectmenu();
      $("#slay_edit_estimated").val(slay['estimated']);
      $("#slay_edit_estimated").selectmenu("refresh");
      $("#slay_edit_hauler").val(dc(slay['hauler']));
      $("#slay_edit_haul_equip").val(dc(slay['haul_equip']));
      $("#slay_edit_fee").val(slay['fees']);
      $("#slay_edit_comments").val(dc(slay['comments']));
   }
          },
          error: onError
      });
}

function init_slayhouse() {
   update_slayhouse('edit_slayhouse', false, false);
   update_slayhouse_active('edit_slayhouse');
}

var other_input_initted = false;

function init_other_input(id) {
   var fd = "";
   if (other_input_initted) {
      fd = new FormData(document.getElementById("other_input_form"));
   }
   create_date(id + "_date", null);
   update_animal_id(id, true, null);
   animal_update(id);
   update_other_dest(id, true, false);
   update_other_reason(id, true, false);
   if (other_input_initted) {
      var mth = fd.get("other_input_date_month");
      var dy = fd.get("other_input_date_day");
      var yr = fd.get("other_input_date_year");
      var an_id = fd.get("other_input_id");
      var reason = fd.get("other_input_reason");
      var dest = fd.get("other_input_dest");

      $("#other_input_date_month").val(mth);
      $("#other_input_date_month").selectmenu("refresh");
      $("#other_input_date_day").val(dy);
      $("#other_input_date_day").selectmenu("refresh");
      $("#other_input_date_year").val(yr);
      $("#other_input_date_year").selectmenu("refresh");
      if (inSelect("other_input_id", dc(an_id))) {
         $("#other_input_id").val(dc(an_id));
         $("#other_input_id").selectmenu("refresh");
         animal_update(id);
      }
      if (inSelect("other_input_reason", dc(reason))) {
         $("#other_input_reason").val(dc(reason));
         $("#other_input_reason").selectmenu("refresh");
      }
      if (inSelect("other_input_dest", dc(dest))) {
         $("#other_input_dest").val(dc(dest));
         $("#other_input_dest").selectmenu("refresh");
      }
   } else {
      other_input_initted = true;
   }
}

var otherDestId = "";

function add_other_dest(hash, id) {
   location.hash = "#add_other_dest";
   returnHash = hash;
   otherDestId = id;
}

function update_other_dest(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_other_dest.php?active=" + encodeURIComponent(active) + "&all=" +
            encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_dest").html(data);
          $("#" + id + "_dest").selectmenu();
          $("#" + id + "_dest").selectmenu("refresh");
       },
       error: onError
   });
}

function update_other_dest_active(id) {
   var dest = $("#" + id + "_dest").val();
   return $.ajax({
       type: "POST",
       url: "get_other_dest_active.php?dest=" + encodeURIComponent(dest),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_other_dest() {
   update_other_dest('edit_dest_other', false, false);
   update_other_dest_active('edit_dest_other');
}

var otherReasonId = "";

function add_other_reason(hash, id) {
   location.hash = "#add_other_reason";
   returnHash = hash;
   otherReasonId = id;
}

function update_other_reason(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_other_reason.php?active=" + encodeURIComponent(active) + 
            "&all=" + encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_reason").html(data);
          $("#" + id + "_reason").selectmenu();
          $("#" + id + "_reason").selectmenu("refresh");
       },
       error: onError
   });
}
   
function update_other_reason_active(id) {
   var reason = $("#" + id + "_reason").val();
   return $.ajax({
       type: "POST",
       url: "get_other_reason_active.php?reason=" + encodeURIComponent(reason),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_other_reason() {
   update_other_reason("edit_reason_other", false, false);
   update_other_reason_active("edit_reason_other");
}

function other_input_validate() {
   var dt = $("#other_input_date_month").val() + "/" + $("#other_input_date_day").val()
            + "/" + $("#other_input_date_year").val();
   var id = $("#other_input_id").val();
   if (id == null) {
      alert("Please select an Animal ID.");
      return false;
   }
   var weight = $("#other_input_wt").val();
   if (weight != "N/A" && (weight == "" || !isFinite(weight) || 
       parseFloat(weight) <= 0)) {
      alert("Please enter a valid weight.");
      return false;
   }
   var reason = $("#other_input_reason").val();
   if (reason == null) {
      alert("Please select a Reason.");
      return false;
   }
   var dest = $("#other_input_dest").val();
   if (dest == null) {
      alert("Please select a Destination.");
      return false;
   }
   var com = $("#other_input_comments").val();

   var con = "Date: " + dt + "\nAnimal ID: " + id + 
             "\nFinal Weight: " + weight + " (lbs.)" +
             "\nReason for Removal: " + reason + "\nDestination: " + dest +
             "\nComments: " + com;

   return confirm("Confirm Entry:\n"+con);
}

function init_other_report() {
   create_date("other_report_from", null);
   create_date("other_report_to", null);
   init_group("other_report", true, null);
   update_other_reason("other_report", true, true);
}

function other_edit_init(id) {
   location.hash = "#other_edit";
   update_other_dest('other_edit', true, false);
   update_other_reason('other_edit', true, false);
   $.ajax({
       type: "POST",
       url: "edit_other_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var other = JSON.parse(data);
      $("#other_edit_auto_id").val(id);
      $("#other_edit_orig_id").val(dc(other['animal_id']));
      create_date("other_edit_date", humanDate(other['remove_date']));
      update_animal_id('other_edit', true, other['animal_id']);
      $("#other_edit_id").selectmenu();
      $("#other_edit_id").val(dc(other['animal_id']));
      $("#other_edit_id").selectmenu("refresh");
      $("#other_edit_reason").selectmenu();
      $("#other_edit_reason").val(dc(other['reason']));
      $("#other_edit_reason").selectmenu("refresh");
      $("#other_edit_dest").selectmenu();
      $("#other_edit_dest").val(dc(other['destination']));
      $("#other_edit_dest").selectmenu("refresh");
      $("#other_edit_wt").val(dc(other['weight']));
      $("#other_edit_comments").val(dc(other['comments']));
   }
          },
          error: onError
      });
}

function other_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_other.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Other removal record successfully deleted.");
                update_animal_id('sale_input', true, null);
                update_animal_id('slay_input', true, null);
                update_animal_id('other_input', true, null);
                $("#other_report_form").submit();
                location.hash = "#other_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function init_paddock_record() {
   $.ajax({
       type: "POST",
       url: "get_paddock_all.php",
       cache: false,
       success: 
function(data, status) {
   $("#paddock_table").html(data);
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function get_feed_type(id, active, all) {
   $.ajax({
       type: "POST",
       async: false,
       url: "get_feed_type.php?active=" + encodeURIComponent(active) +
            "&all=" + encodeURIComponent(all),
       cache: false,
       success: 
function(data, status) {
   $("#" + id + "_type").selectmenu();
   $("#" + id + "_type").html(data);
   $("#" + id + "_type").selectmenu("refresh");
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function feed_input_init(id) {
   create_date(id + "_date");
   get_feed_type(id, true, false);
   var type = $("#" + id + "_type").val();
   $.ajax({
       type: "POST",
       url: "feed_input_init_data.php?type=" + encodeURIComponent(type),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var feed = JSON.parse(data);
      $("#" + id + "_subtype").selectmenu();
      $("#" + id + "_subtype").html(feed['subtype']);
      $("#" + id + "_subtype").selectmenu("refresh");
      $("#" + id + "_group").selectmenu();
      $("#" + id + "_group").html(feed['group']);
      $("#" + id + "_group").selectmenu("refresh");
      $("#" + id + "_vendor").selectmenu();
      $("#" + id + "_vendor").html(feed['vendor']);
      $("#" + id + "_vendor").selectmenu("refresh");
      $("#" + id + "_unit").selectmenu();
      $("#" + id + "_unit").html(feed['unit']);
      $("#" + id + "_unit").selectmenu("refresh");
   }
          },
          error: onError
      });
}

var feed_typeId = "";

function add_feed_type(hash, id) {
   location.hash = "#add_feed_mtype";
   returnHash = hash;
   feed_typeId = id;
}

function update_feed_type(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_feed_type.php?active=" + encodeURIComponent(active) + 
            "&all=" + encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_type").html(data);
          $("#" + id + "_type").selectmenu();
          $("#" + id + "_type").selectmenu("refresh");
       },
       error: onError
   });
}
   
function update_feed_type_active(id) {
   var type = $("#" + id + "_type").val();
   return $.ajax({
       type: "POST",
       url: "get_feed_type_active.php?type=" + encodeURIComponent(type),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_feed_type() {
   update_feed_type("edit_feed_type", false, false);
   update_feed_type_active("edit_feed_type");
}

var feed_subtypeId = "";

function add_feed_subtype(hash, id) {
   update_feed_type("add_feed_subtype", true, false);
   var type = $("#" + id + "_type").val();
   $("#add_feed_subtype_type").selectmenu();
   $("#add_feed_subtype_type").val(dc(type));
   $("#add_feed_subtype_type").selectmenu("refresh");
   location.hash = "#add_feed_subtype";
   returnHash = hash;
   feed_subtypeId = id;
}

function update_feed_subtype(id, active, all) {
   var type = $("#" + id + "_type").val();
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_feed_subtype.php?active=" + encodeURIComponent(active) + 
            "&all=" + encodeURIComponent(all) + 
            "&type=" + encodeURIComponent(type),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_subtype").html(data);
          $("#" + id + "_subtype").selectmenu();
          $("#" + id + "_subtype").selectmenu("refresh");
       },
       error: onError
   });
}
   
function update_feed_subtype_active(id) {
   var type = $("#" + id + "_type").val();
   var subtype = $("#" + id + "_subtype").val();
   return $.ajax({
       type: "POST",
       url: "get_feed_subtype_active.php?type=" + encodeURIComponent(type) +
            "&subtype=" + encodeURIComponent(subtype),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_feed_subtype() {
   update_feed_type("edit_feed_subtype", false, false);
   update_feed_subtype("edit_feed_subtype", false, false);
   update_feed_subtype_active("edit_feed_subtype");
}

var vendorId = "";

function add_vendor(hash, id) {
   location.hash = "#add_vendor";
   returnHash = hash;
   vendorId = id;
}

function update_vendor(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_vendor.php?active=" + encodeURIComponent(active) + 
            "&all=" + encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_vendor").html(data);
          $("#" + id + "_vendor").selectmenu();
          $("#" + id + "_vendor").selectmenu("refresh");
       },
       error: onError
   });
}
   
function update_vendor_active(id) {
   var vendor = $("#" + id + "_vendor").val();
   return $.ajax({
       type: "POST",
       url: "get_vendor_active.php?vendor=" + encodeURIComponent(vendor),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_vendor() {
   update_vendor("edit_vendor", false, false);
   update_vendor_active("edit_vendor");
}

var feed_unitId = "";

function add_feed_unit(hash, id) {
   location.hash = "#add_feed_unit";
   returnHash = hash;
   feed_unitId = id;
}

function update_feed_unit(id, active, all) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_feed_unit.php?active=" + encodeURIComponent(active) + 
            "&all=" + encodeURIComponent(all),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_unit").html(data);
          $("#" + id + "_unit").selectmenu();
          $("#" + id + "_unit").selectmenu("refresh");
       },
       error: onError
   });
}
   
function update_feed_unit_active(id) {
   var feed_unit = $("#" + id + "_unit").val();
   return $.ajax({
       type: "POST",
       url: "get_feed_unit_active.php?feed_unit=" + encodeURIComponent(feed_unit),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_active").val(data);
          $("#" + id + "_active").selectmenu();
          $("#" + id + "_active").selectmenu("refresh");
       },
       error: onError
   });
}

function init_edit_feed_unit() {
   update_feed_unit("edit_feed_unit", false, false);
   update_feed_unit_active("edit_feed_unit");
}

function update_feed_total() {
   var units = $("#feed_input_purchased").val();
   var price = $("#feed_input_price").val();
   if (price != "" && units != "") {
      var tot = parseFloat(price) * parseFloat(units);
      $("#feed_input_total").val(tot.toFixed(2));
   }
}

function update_feed_weight() {
   var units = $("#feed_input_purchased").val();
   var wt = $("#feed_input_unit_weight").val();
   if (wt != "" && units != "") {
      var tot = parseFloat(wt) * parseFloat(units);
      $("#feed_input_total_weight").val(tot.toFixed(2));
   }
}

function feed_input_validate() {
   var dt = $("#feed_input_date_month").val() + "/" + $("#feed_input_date_day").val()
            + "/" + $("#feed_input_date_year").val();
   var type = $("#feed_input_type").val();
   if (type == null) {
      alert("Please select Feed Major Type.");
      return false;
   }
   var subtype = $("#feed_input_subtype").val();
   if (subtype == null) {
      alert("Please select Feed Type Details.");
      return false;
   }
   var grp = $("#feed_input_group").val();
   if (grp == null) {
      alert("Please select the animal group that the feed is for.");
      return false;
   }
   var vendor = $("#feed_input_vendor").val();
   if (vendor == null) {
      alert("Please select a Vendor.");
      return false;
   }
   var unit = $("#feed_input_unit").val();
   if (unit == null) {
      alert("Please select a Feed Unit.");
      return false;
   }
   var purch = $("#feed_input_purchased").val();
   if (purch == "" || !isFinite(purch) || parseFloat(purch) <= 0) {
      alert("Please enter a valid number of units purchased.");
      return false;
   }
   var price = $("#feed_input_price").val();
   if (price == "" || !isFinite(price) || parseFloat(price) <= 0) {
      alert("Please enter a valid price per unit.");
      return false;
   }
   var weight = $("#feed_input_unit_weight").val();
   if (weight == "" || !isFinite(weight) || parseFloat(weight) <= 0) {
      alert("Please enter a valid weight per unit.");
      return false;
   }
   var com = $("#feed_input_comments").val();

   var con = "Date: " + dt + "\nFeed Major Type: " + type + 
             "\nFeed Type Details: " + subtype + "\nFor: " + grp + 
             "\nVendor: " + vendor + "\nFeed Unit: " + unit +
             "\nUnits Purchased: " + purch + "\nPrice per Unit: $" + price +
             "\nWeight per Unit: " + weight + " (lbs.)\nComments: " + com;

   return confirm("Confirm Entry:\n"+con);
}

function feed_report_init() {
   create_date("feed_report_from");
   create_date("feed_report_to");
   update_feed_type("feed_report", true, true);
   update_feed_subtype("feed_report", true, true);
   init_group("feed_report", true, null);
   update_vendor("feed_report", true, true);
}

function feed_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_feed_purchase.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Feed purchase record successfully deleted.");
                location.hash = "#feed_report";
                $("#feed_report_form").submit();
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function feed_edit_init(id) {
   feed_input_init("feed_edit");
   $.ajax({
       type: "POST",
       url: "feed_edit_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var feed = JSON.parse(data);
  
      $("#feed_edit_auto_id").val(id);
      create_date("feed_edit_date", humanDate(feed['purch_date']));
      $("#feed_edit_type").selectmenu();
      $("#feed_edit_type").val(dc(feed['type']));
      $("#feed_edit_type").selectmenu("refresh");
      update_feed_subtype("feed_edit", true, false);
      $("#feed_edit_subtype").selectmenu();
      $("#feed_edit_subtype").val(dc(feed['subtype']));
      $("#feed_edit_subtype").selectmenu("refresh");
      $("#feed_edit_group").selectmenu();
      $("#feed_edit_group").val(dc(feed['animal_group']));
      $("#feed_edit_group").selectmenu("refresh");
      $("#feed_edit_vendor").selectmenu();
      $("#feed_edit_vendor").val(dc(feed['vendor']));
      $("#feed_edit_vendor").selectmenu("refresh");
      $("#feed_edit_unit").selectmenu();
      $("#feed_edit_unit").val(dc(feed['unit']));
      $("#feed_edit_unit").selectmenu("refresh");
      $("#feed_edit_purchased").val(feed['purchased']);
      $("#feed_edit_price").val(feed['price_unit']);
      $("#feed_edit_unit_weight").val(feed['weight_unit']);
      $("#feed_edit_comments").val(dc(feed['comments']));
   }
          },
          error: onError
      });
}

function init_health_record() {
   create_date("herd_health_from");
   create_date("herd_health_to");
}

function init_prod_record() {
   create_date("herd_prod_from");
   create_date("herd_prod_to");
}

function check_con() {
   check_dup("foo"); // only to check database connection
}

var tasks;
var groups;
var subgroups;
var firstsubs;

function daily_task_vals() {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_daily_task_vals.php",
       cache: false,
       data: "",
       success: function (data, status) {
          var content = JSON.parse(data);
          tasks = content['tasks'];
          groups = content['groups'];
          subgroups = content['subgroups'];
       },
       error: onError
   });
}

var taskId = "";

function init_daily_task() {
   taskId = "daily_task";
   create_date("add_daily_task_date", null);
   update_daily_task_list();
}

var num_daily_task_rows = 0;

function update_daily_task_list() {
   daily_task_vals();
   var dt = $("#add_daily_task_date_year").val() + "-" + 
            $("#add_daily_task_date_month").val() + "-" + 
            $("#add_daily_task_date_day").val();
   $("#daily_task_table").find("tr:gt(0)").remove();
   $(".killme").remove();
   num_daily_task_rows = 0;
   $.ajax({
       type: "POST",
       url: "daily_task_by_date.php?date=" + encodeURIComponent(dt),
       cache: false,
       data: "",
       success: 
function(data, status) {
   var content = JSON.parse(data);
   for (var i = 1; i <= content.length; i++) {
      var con = content[i - 1];
      add_daily_task_row(true);
      $("#daily_task_task_" + i).val(con['task']);
      $("#daily_task_task_" + i).selectmenu("refresh");
      $("#daily_task_comment_" + i).val(con['comments']);
      $("#daily_task_group_" + i).val(con['animal_group']);
      $("#daily_task_group_" + i).selectmenu("refresh");
      set_subgroup("daily_task", i);
      $("#daily_task_subgroup_" + i).val(con['sub_group']);
      $("#daily_task_subgroup_" + i).selectmenu("refresh");

      $("#daily_task_workers_" + i).val(con['workers']);
      $("#daily_task_minutes_" + i).val(con['minutes']);
      $("#daily_task_user_" + i).val(con['userid']);
      $("#daily_task_complete_" + i).val(con['complete']);
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function remove_daily_row(id) {
   $("#daily_task_row_" + id).remove();
}

function set_subgroup(id, row) {
   var group = $("#" + id + "_group_" + row).val();
   $("#" + id + "_subgroup_" + row).html(subgroups[group]);
   $("#" + id + "_subgroup_" + row).selectmenu();
   $("#" + id + "_subgroup_" + row).selectmenu("refresh");
}

function add_daily_task_row(fromDB) {
   if (!fromDB) {
      daily_task_vals();
   }
   num_daily_task_rows++;
   $("#num_daily_task_rows").val(num_daily_task_rows);
   var row = "<tr id='daily_task_row_" + num_daily_task_rows + 
             "'><td><select id='daily_task_task_" + num_daily_task_rows + 
             "' name='daily_task_task_" + num_daily_task_rows + "'>" + tasks + 
             "</select></td><td><textarea id='daily_task_comment_" +
             num_daily_task_rows  + "' name='daily_task_comment_" +
             num_daily_task_rows + "' style='width:98%'></textarea></td><td>" +
             "<select id='daily_task_group_" + num_daily_task_rows + 
             "' name='daily_task_group_" + num_daily_task_rows + "' " +
             " onchange='set_subgroup(\"daily_task\", " + num_daily_task_rows +
             ");'>" + 
             groups + "</select></td><td><select id='daily_task_subgroup_" + 
             num_daily_task_rows + "' name='daily_task_subgroup_" + 
             num_daily_task_rows + "'><option value='ALL'>ALL</option>" +
             "</select></td><td><input type='button' class='ui-btn' " +
             "id='daily_task_remove_" + num_daily_task_rows + "' " +
             "name='daily_task_remove_" + num_daily_task_rows + "' " +
             "value='Remove' style='width:100%' onclick='remove_daily_row(" +
              num_daily_task_rows + ");'></td></tr>";
   $("#daily_task_table").append(row);
   $("#daily_task_task_" + num_daily_task_rows).selectmenu();
   $("#daily_task_task_" + num_daily_task_rows).selectmenu("refresh");
   $("#daily_task_group_" + num_daily_task_rows).selectmenu();
   $("#daily_task_group_" + num_daily_task_rows).selectmenu("refresh");
   $("#daily_task_subgroup_" + num_daily_task_rows).selectmenu();
   $("#daily_task_subgroup_" + num_daily_task_rows).selectmenu("refresh");

   $("#daily_task_form").append("<input type='hidden' id='daily_task_workers_" +
      num_daily_task_rows + "' name='daily_task_workers_" + 
      num_daily_task_rows + "' value='1' class='killme'/>");
   $("#daily_task_form").append("<input type='hidden' id='daily_task_minutes_" +
      num_daily_task_rows + "' name='daily_task_minutes_" + 
      num_daily_task_rows + "' value='1' class='killme'/>");
   $("#daily_task_form").append("<input type='hidden' id='daily_task_user_" +
      num_daily_task_rows + "' name='daily_task_user_" + 
      num_daily_task_rows + "' value='' class='killme'/>");
   $("#daily_task_form").append("<input type='hidden' id='daily_task_complete_" +
      num_daily_task_rows + "' name='daily_task_complete_" + 
      num_daily_task_rows + "' value='0' class='killme'/>");
}

function daily_task_validate() {
   $("#daily_task_table > tbody > tr").each(function (i, row) {
      var id = $(row).attr("id").split("_")[3];
      var task = $("#daily_task_task_" + id).val();
      if (task == null) {
         alert("Please select a task in row " + (i + 1) + ".");
         return false;
      }
      var group = $("#daily_task_group_" + id).val();
      if (group == null) {
         alert("Please select an animal group in row " + (i + 1) + ".");
         return false;
      }
      var subgroup = $("#daily_task_subgroup_" + id).val();
      if (subgroup == null) {
         alert("Please select a subgroup in row " + (i + 1) + ".");
         return false;
      }
   });
   return true;
}

function add_task(hash) {
   location.hash = "#add_task";
   returnHash = hash;
}

function init_edit_task() {
   update_task("edit_task", false);
   update_edit_task();
}

function update_task(id, active) {
   return $.ajax({
       type: "POST",
       async: false,
       url: "get_task.php?active=" + encodeURIComponent(active),
       cache: false,
       data: "",
       success: function (data, status) {
          $("#" + id + "_task").html(data);
          $("#" + id + "_task").selectmenu();
          $("#" + id + "_task").selectmenu("refresh");
       },
       error: onError
   });
}

function update_edit_task() {
   var task = $("#edit_task_task").val();
   return $.ajax({
       type: "POST",
       url: "task_edit_data.php?task=" + encodeURIComponent(task),
       cache: false,
       data: "",
       success: function (data, status) {
          if (data.startsWith("Error")) {
             alert(data);
          } else {
             $("#edit_task_active").val(data);
             $("#edit_task_active").selectmenu();
             $("#edit_task_active").selectmenu("refresh");
          }
       },
       error: onError
   });
}

function init_daily_task_list() {
   // daily_task_vals();
   create_date("daily_task_date", null);
   update_daily_task_table();
}

function update_daily_task_table() {
   $("#daily_chores_table").find("tr:gt(0)").remove();
   $(".killme").remove();
   var dt = $("#daily_task_date_year").val() + "-" + 
            $("#daily_task_date_month").val() + "-" + 
            $("#daily_task_date_day").val();
   $.ajax({
       type: "POST",
       url: "daily_task_by_date.php?date=" + encodeURIComponent(dt),
       cache: false,
       data: "",
       success: 
function(data, status) {
   var content = JSON.parse(data);
   
   $("#num_daily_chores").val(content.length);
   for (var i = 0; i < content.length; i++) {
      var con = content[i];
      var row = "<tr id='daily_chores_row_" + i + "'><td>" + con['task'] + 
                "</td><td><input type='checkbox' id='daily_chores_complete_" +
                i + "' name='daily_chores_complete_" + i + "'";
       if (parseInt(con['complete']) == 1) {
          row += " checked";
       }
       row += ">";
       row += "<td><textarea id='daily_chores_comments_" + 
               i + "' name='daily_chores_comments_" + i + "'>" + 
               con['comments'] + "</textarea></td><td>" +
               con['animal_group'] + "</td><td>" + con['sub_group'] + 
               "</td><td><input type='number' min='1' step='1' " +
               "style='width:5em' " +
               "id='daily_chores_workers_" + i + 
               "' name='daily_chores_workers_" + i + "' value='" + 
               con['workers'] + "'></td><td><input type='number' min='1' " +
               "step='1' id='daily_chores_minutes_" + i + "' " +
               "name='daily_chores_minutes_" + i + "' value='" +
                con['minutes'] + "' style='width:5em'></td>" +
                "</td></tr>";
       $("#daily_chores_table").append(row);
       $("#daily_chores_form").append("<input type='hidden' id='daily_chores_id_" +
          i + "' name='daily_chores_id_" + 
          i + "' value='" + con['id'] + "' class='killme'/>");
   }
},
       contentType: false,
       processData: false,
       error: onError
    });
}

/*
function update_chore_row(row, id) {
   var com = $("#daily_chores_comments_" + row).val();
   var work = $("#daily_chores_workers_" + row).val();
   var min = $("#daily_chores_minutes_" + row).val();
   var comp = $("#daily_chores_complete_" + row).is(":checked");
   $.ajax({
       type: "POST",
       url: "update_chore_row.php?id=" + id + "&comment=" + 
               encodeURIComponent(com) + "&workers=" + work + "&minutes=" +
               min + "&complete=" + encodeURIComponent(comp),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data == "success!") {
      alert("Task updated successfully.");
      update_daily_task_table();
   } else {
      alert(data);
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}
*/

function init_labor_report() {
   create_date("labor_report_from", null);
   create_date("labor_report_to", null);
   $.ajax({
      type: "POST",
      url: "get_task.php?active=true",
      cache: false,
      data: "",
      success: function (data, status) {
         var dat = "<option value='%'>ALL</option>" + data;
         $("#labor_report_task").html(dat);
         $("#labor_report_task").selectmenu();
         $("#labor_report_task").selectmenu("refresh");
      },
      error: onError
   });
   init_group("labor_report", true, null);
}

function labor_delete(id) {
   if (confirm("Are you sure that you want to delete this record?")) {
      return $.ajax({
          type: "POST",
          url: "delete_labor.php?id=" + encodeURIComponent(id),
          cache: false,
          data: "",
          success: function (data, status) {
             if (data == "success!") {
                alert("Labor record successfully deleted.");
                $("#labor_report_form").submit();
                location.hash = "#labor_report";
             } else {
                alert(data);
             }
          },
          error: onError
      });
   }
}

function init_labor_input(id) {
   create_date(id + "_date", null);
   $.ajax({
      type: "POST",
      url: "get_task.php?active=true",
      cache: false,
      data: "",
      success: function (data, status) {
         $("#" + id + "_task").html(data);
         $("#" + id + "_task").selectmenu();
         $("#" + id + "_task").selectmenu("refresh");
      },
      error: onError
   });
   init_group(id, true, null);
}

function labor_input_validate() {
   var dt = $("#labor_input_date_month").val() + "/" + $("#labor_input_date_day").val()
            + "/" + $("#labor_input_date_year").val();
   var task = $("#labor_input_task").val();
   if (task == null) {
      alert("Please select a Task.");
      return false;
   }
   var grp = $("#labor_input_group").val();
   if (grp == "%") {
      grp = "ALL";
   }
   if (grp == null) {
      alert("Please select the animal group that the labor is for.");
      return false;
   }
   var subgrp = $("#labor_input_subgroup").val();
   if (subgrp == "%") {
      subgrp = "ALL";
   }
   if (subgrp == null) {
      alert("Please select the animal subgroup that the labor is for.");
      return false;
   }
   var work = $("#labor_input_workers").val();
   if (work == "" || !isFinite(work) || parseFloat(work) <= 0) {
      alert("Please enter a valid number of workers.");
      return false;
   }
   var min = $("#labor_input_minutes").val();
   if (min == "" || !isFinite(min) || parseFloat(min) <= 0) {
      alert("Please enter a valid number of minutes.");
      return false;
   }
   var com = $("#labor_input_comments").val();

   var con = "Date: " + dt + "\nTask: " + task + "\nComments: " + com +
             "\nAnimal Group: " + grp + "\nSubgroup: " + subgrp + 
             "\nNumber of Workers: " + work + "\nNumber of Minutes: " + min;

   return confirm("Confirm Entry:\n"+con);
}

function labor_edit_init(id) {
   init_labor_input("labor_edit");
   $.ajax({
       type: "POST",
       url: "labor_edit_data.php?id=" + encodeURIComponent(id),
       cache: false,
       data: "",
       success: 
function(data, status) {
   if (data.startsWith("Error")) {
      alert(data);
   } else {
      var labor = JSON.parse(data);
      $("#labor_edit_auto_id").val(id);
      $("#labor_edit_origdate").val(labor['list_date']);
      create_date("labor_edit_date", humanDate(labor['list_date']));
      $("#labor_edit_task").selectmenu();
      $("#labor_edit_task").val(dc(labor['task']));
      $("#labor_edit_task").selectmenu("refresh");
      $("#labor_edit_comments").val(dc(labor['comments']));
      var grp = dc(labor['animal_group']);
      if (grp == "ALL") {
         grp = "%";
      }
      init_group("labor_edit", true, grp);
      var subgrp = dc(labor['sub_group']);
      if (subgrp == "ALL") {
         subgrp = "%";
      }
      $("#labor_edit_subgroup").selectmenu();
      $("#labor_edit_subgroup").val(subgrp);
      $("#labor_edit_subgroup").selectmenu("refresh");
      $("#labor_edit_workers").val(labor['workers']);
      $("#labor_edit_minutes").val(labor['minutes']);
      if (parseInt(labor['complete']) == 1) {
         $("#labor_edit_complete")[0].checked = true;
      }  else {
         $("#labor_edit_complete")[0].checked = false;
      }
      $("#labor_edit_complete").checkboxradio("refresh");
   }
          },
          error: onError
      });
}

var num_recur_task_rows = 0;

function load_recur_task() {
   taskId = "recur_task";
   daily_task_vals();
   $("#recur_task_table").find("tr:gt(0)").remove();
   num_recur_task_rows = 0;
   $.ajax({
       type: "POST",
       url: "get_recur_tasks.php",
       cache: false,
       data: "",
       success: 
function(data, status) {
   var content = JSON.parse(data);
   for (var i = 1; i <= content.length; i++) {
      var con = content[i - 1];
      add_recur_task_row();
      $("#recur_task_task_" + i).val(con['task']);
      $("#recur_task_task_" + i).selectmenu("refresh");
      $("#recur_task_comment_" + i).val(con['comments']);
      $("#recur_task_group_" + i).val(con['animal_group']);
      $("#recur_task_group_" + i).selectmenu("refresh");
      set_subgroup("recur_task", i);
      $("#recur_task_subgroup_" + i).val(con['sub_group']);
      $("#recur_task_subgroup_" + i).selectmenu("refresh");
      $("#recur_task_workers_" + i).val(con['workers']);
      $("#recur_task_minutes_" + i).val(con['minutes']);
      $("#recur_task_occurs_" + i).val(con['recur']);
      $("#recur_task_occurs_" + i).selectmenu("refresh");
      var dtArr = con['start_date'].split('-');
      dtArr[1] = parseInt(dtArr[1]);
      dtArr[2] = parseInt(dtArr[2]);
      $("#recur_task_date_" + i + "_year").val(dtArr[0]);
      $("#recur_task_date_" + i + "_year").selectmenu("refresh");
      $("#recur_task_date_" + i + "_month").val(dtArr[1]);
      $("#recur_task_date_" + i + "_month").selectmenu("refresh");
      $("#recur_task_date_" + i + "_day").val(dtArr[2]);
      $("#recur_task_date_" + i + "_day").selectmenu("refresh");
   }
},
       contentType: false,
       processData: false,
       error: onError
   });
}

function add_recur_task_row() {
   num_recur_task_rows++;
   $("#num_recur_task_rows").val(num_recur_task_rows);
   var d = new Date();
   var year = d.getFullYear();
   var mth = d.getMonth() + 1;
   var day = d.getDate();
   var row = "<tr id='recur_task_row_" + num_recur_task_rows + 
             "'><td>" + date_string('recur_task_date_' + 
             num_recur_task_rows, year) + "</td>" + 
             "<td><select id='recur_task_task_" + num_recur_task_rows + 
             "' name='recur_task_task_" + num_recur_task_rows + "'>" + tasks + 
             "</select></td><td>" +
             "<textarea id='recur_task_comment_" +
             num_recur_task_rows  + "' name='recur_task_comment_" +
             num_recur_task_rows + "' style='width:100%'></textarea></td><td>" +
             "<select id='recur_task_group_" + num_recur_task_rows + 
             "' name='recur_task_group_" + num_recur_task_rows + "' " +
             " onchange='set_subgroup(\"recur_task\", " + num_recur_task_rows + 
             ");'>" + 
             groups + "</select></td><td><select id='recur_task_subgroup_" + 
             num_recur_task_rows + "' name='recur_task_subgroup_" + 
             num_recur_task_rows + "'><option value='ALL'>ALL</option>" +
             "</select></td><td><input type='number' min='1' step='1' " +
             "style='width:4em' " + "id='recur_task_workers_" + 
             num_recur_task_rows + "' name='recur_task_workers_" +
             num_recur_task_rows + "' value='1'" + "></td><td>" +
             "<input type='number' min='1' " + "step='1' " +
             "id='recur_task_minutes_" + num_recur_task_rows + "' " +
             "name='recur_task_minutes_" + num_recur_task_rows  + 
             "' value='1' style='width:5em'></td>" + 
             "<td><select id='recur_task_occurs_" + 
             num_recur_task_rows + "' name='recur_task_occurs_" + 
             num_recur_task_rows + "'><option value='DAILY'>DAILY</option>" +
             "<option value='WEEKLY'>WEEKLY</option>" +
             "<option value='BIWEEKLY'>BIWEEKLY</option>" +
             "<option value='MONTHLY'>MONTHLY</option></select></td>" +
             "<td><input type='button' class='ui-btn' " +
             "id='recur_task_remove_" + num_recur_task_rows + "' " +
             "name='recur_task_remove_" + num_recur_task_rows + "' " +
             "value='Remove' style='width:100%' onclick='remove_recur_row(" +
              num_recur_task_rows + ");'></td></tr>";
   $("#recur_task_table").append(row);
   $("#recur_task_task_" + num_recur_task_rows).selectmenu();
   $("#recur_task_task_" + num_recur_task_rows).selectmenu("refresh");
   $("#recur_task_group_" + num_recur_task_rows).selectmenu();
   $("#recur_task_group_" + num_recur_task_rows).selectmenu("refresh");
   $("#recur_task_subgroup_" + num_recur_task_rows).selectmenu();
   $("#recur_task_subgroup_" + num_recur_task_rows).selectmenu("refresh");
   $("#recur_task_occurs_" + num_recur_task_rows).selectmenu();
   $("#recur_task_occurs_" + num_recur_task_rows).selectmenu("refresh");

   var id = 'recur_task_date_' + num_recur_task_rows;

   $("#" + id + "_month").selectmenu();
   $("#" + id + "_month").val(mth);
   $("#" + id + "_month").selectmenu("refresh");
   $("#" + id + "_day").selectmenu();
   $("#" + id + "_day").val(day);
   $("#" + id + "_day").selectmenu("refresh");
   $("#" + id + "_year").selectmenu();
   $("#" + id + "_year").val(year);
   $("#" + id + "_year").selectmenu("refresh");
}

function recur_task_validate() {
   $("#recur_task_table > tbody > tr").each(function (i, row) {
      var id = $(row).attr("id").split("_")[3];
      var task = $("#recur_task_task_" + id).val();
      if (task == null) {
         alert("Please select a task in row " + (i + 1) + ".");
         return false;
      }
      var group = $("#recur_task_group_" + id).val();
      if (group == null) {
         alert("Please select an animal group in row " + (i + 1) + ".");
         return false;
      }
      var subgroup = $("#recur_task_subgroup_" + id).val();
      if (subgroup == null) {
         alert("Please select a subgroup in row " + (i + 1) + ".");
         return false;
      }
   });
   return true;
}

function remove_recur_row(id) {
   $("#recur_task_row_" + id).remove();
}

function daily_chores_validate() {
   $("#daily_chores_table > tbody > tr").each(function (i, row) {
      var id = $(row).attr("id").split("_")[3];
      var workers = $("#daily_chores_workers_" + id).val();
      if (workers == "" || !isFinite(workers) || parseInt(workers) < 1) {
         alert("Please enter a valid number of workers in row " + (i + 1) + ".");
         return false;
      }
      var minutes = $("#daily_chores_minutes_" + id).val();
      if (minutes == "" || !isFinite(minutes) || parseInt(minutes) < 1) {
         alert("Please enter a valid number of minutes in row " + (i + 1) + ".");
         return false;
      }
   });
   return true;
}

$(document).ready(function() {

   check_dup("foo"); // only to check database connection when application
                     // starts running

    // $("#birth_input_form").submit(function(){
    $("#birth_input_form").on('submit', function(){

       if (birth_input_validate()) {
          $.ajax({
              type: "POST",
              url: "birth_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Animal Identifier.");
   } else if (data == "success!") {
      showAlert("birth_notification", "Animal record successfully added.");
      update_parents($("#birth_group").val(), $("#birth_mother").val(),
                     $("#birth_father").val());
      clearForm("birth_file");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#birth_report_form").submit(function(){
    $("#birth_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "birth_report.php",
           cache: false,
           // data: formData,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#birth_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#birth_edit_form").submit(function(){
    $("#birth_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "birth_edit.php",
           cache: false,
           // data: formData,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate animal identifier.");
   } else if (data == "success!") {
     alert("Animal record successfully edited.");
     location.hash = "#birth_report";
     $("#birth_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_group_form").submit(function(){
    $("#add_group_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_group.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate animal group.");
   } else if (data == "success!") {
      showAlert("add_group_notification", 
                "Animal group successfully added.");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_breed_form").submit(function(){
    $("#add_breed_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_breed.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate breed.");
   } else if (data == "success!") {
      alert("Breed successfully added.");
      location.hash = returnHash;
      update_breed(breedId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_subgroup_form").submit(function(){
    $("#add_subgroup_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_subgroup.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1452")) {
      alert("Error: Duplicate subgroup.");
   } else if (data == "success!") {
      alert("Subgroup successfully added.");
      location.hash = returnHash;
      update_subgroup(subgroupId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_origin_form").submit(function(){
    $("#add_origin_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_origin.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate origin.");
   } else if (data == "success!") {
      alert("Origin successfully added.");
      location.hash = returnHash;
      update_origin(originId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_dest_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_dest.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate destination.");
   } else if (data == "success!") {
      alert("Destination successfully added.");
      location.hash = returnHash;
      update_dest(destId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_reason_form").submit(function(){
    $("#add_reason_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_reason.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate reason.");
   } else if (data == "success!") {
      alert("Reason successfully added.");
      location.hash = returnHash;
      update_reason(reasonId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_medication_form").submit(function(){
    $("#add_medication_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_medication.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate medication.");
   } else if (data == "success!") {
      alert("Medication successfully added.");
      location.hash = returnHash;
      update_medication(medicationId);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#vet_input_form").submit(function(){
    $("#vet_input_form").on('submit', function(){
    // $(document).on('submit', "#vet_input_form", function(){
    // START - potential fix on iOS?

       if (vet_input_validate()) {
  
          $.ajax({
              type: "POST",
              url: "vet_input.php",
              cache: false,
              // data: formData,
              data: new FormData(this),
              success: 
function(data, status) {
   while (num_med_rows > 0) {
      remove_med_row();
   }
   if (data == "success!") {
      showAlert("vet_notification", "Vet record successfully added.");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#vet_report_form").submit(function(){
    $("#vet_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "vet_report.php",
           cache: false,
           // data: formData,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#vet_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#vet_edit_form").submit(function(){
    $("#vet_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "vet_edit.php",
           cache: false,
           // data: formData,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1452")) {
      alert("Error: Invalid animal identifier.");
   } else if (data == "success!") {
     alert("Vet record successfully edited.");
     location.hash = "#vet_report";
     $("#vet_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#egg_input_form").submit(function(){
    $("#egg_input_form").on('submit', function(){

       if (egg_input_validate()) {
  
          $.ajax({
              type: "POST",
              url: "egg_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("egg_notification", "Egg log record successfully added.");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#egg_report_form").submit(function(){
    $("#egg_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "egg_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#egg_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#egg_edit_form").submit(function(){
    $("#egg_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "egg_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Egg log record successfully edited.");
     location.hash = "#egg_report";
     $("#egg_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_wormer_form").submit(function(){
    $("#add_wormer_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_wormer.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate worming product.");
   } else if (data == "success!") {
      alert("Worming product successfully added.");
      location.hash = returnHash;
      update_wormer(wormerId);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#sheep_care_input_form").submit(function(){
    $("#sheep_care_input_form").on('submit', function(){

       if (sheep_care_input_validate()) {
  
          $.ajax({
              type: "POST",
              url: "sheep_care_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("sheep_care_input_notification", "Sheep/goat care record successfully added.");
      $("#sheep_care_input_comments").val("");
      $("#sheep_care_input_weight").val("");
      update_sheep_care_ids();
      animal_update('sheep_care_input');
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#sheep_care_report_form").submit(function(){
    $("#sheep_care_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "sheep_care_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#sheep_care_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#sheep_care_edit_form").submit(function(){
    $("#sheep_care_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "sheep_care_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Sheep/Goat care record successfully edited.");
     $("#sheep_care_report_form").submit();
     location.hash = "#sheep_care_report";
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_subgroups_form").submit(function(){
    $("#edit_subgroups_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "edit_subgroups.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   $("#subgroups_edit_div").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_forage_form").submit(function(){
    $("#add_forage_form").on('submit', function(){
       if (forage_validate()) {
        $.ajax({
           type: "POST",
           url: "add_forage.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate forage.");
   } else if (data == "success!") {
      alert("Forage successfully added.");
      location.hash = returnHash;
      update_forage(paddockId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
   }
   return false;
    });

    // $("#add_paddock_form").submit(function(){
    $("#add_paddock_form").on('submit', function(){
       if (paddock_validate()) {
        $.ajax({
           type: "POST",
           url: "add_paddock.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Paddock ID.");
   } else if (data == "success!") {
      showAlert("add_paddock_notification", "Paddock successfully added.");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
   }
   return false;
    });

    // $("#move_input_form").submit(function(){
    $("#move_input_form").on('submit', function(){
       if (move_input_validate()) {
  
          $.ajax({
              type: "POST",
              url: "move_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("move_input_notification", "Grazing move record successfully added.");
      $("#move_input_comments").val("");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#move_report_form").submit(function(){
    $("#move_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "move_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#move_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_breed_form").submit(function(){
    $("#edit_breed_form").on('submit', function(){
        var breed = $("#edit_breed_breed").val();
        var newbreed = $("#edit_breed_newbreed").val().toUpperCase();
        if (newbreed == "") {
           newbreed = breed;
        }
        $.ajax({
           type: "POST",
           url: "edit_breed.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Breed.");
   } else if (data == "success!") {
     showAlert("edit_breed_notification",
               "Breed successfully edited.");
     update_breed("edit_breed", false);
     $("#edit_breed_breed").val(newbreed);
     $("#edit_breed_breed").selectmenu();
     $("#edit_breed_breed").selectmenu("refresh");
     update_breed_active("edit_breed");
     $("#edit_breed_newbreed").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_subgroup_form").submit(function(){
    $("#edit_subgroup_form").on('submit', function(){
        var subgroup = $("#edit_subgroup_subgroup").val();
        var newsubgroup = $("#edit_subgroup_newsubgroup").val().toUpperCase();
        if (newsubgroup == "") {
           newsubgroup = subgroup;
        }
        $.ajax({
           type: "POST",
           url: "edit_subgroup.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Subgroup.");
   } else if (data == "success!") {
     showAlert("edit_subgroup_notification",
               "Subgroup successfully edited.");
     update_subgroup("edit_subgroup", false);
     $("#edit_subgroup_subgroup").val(newsubgroup);
     $("#edit_subgroup_subgroup").selectmenu();
     $("#edit_subgroup_subgroup").selectmenu("refresh");
     update_subgroup_active("edit_subgroup");
     $("#edit_subgroup_newsubgroup").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_origin_form").submit(function(){
    $("#edit_origin_form").on('submit', function(){
        var origin = $("#edit_origin_origin").val();
        var neworigin = $("#edit_origin_neworigin").val().toUpperCase();
        if (neworigin == "") {
           neworigin = origin;
        }
        $.ajax({
           type: "POST",
           url: "edit_origin.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Origin.");
   } else if (data == "success!") {
     showAlert("edit_origin_notification",
               "Origin successfully edited.");
     update_origin("edit_origin", false);
     $("#edit_origin_origin").val(neworigin);
     $("#edit_origin_origin").selectmenu();
     $("#edit_origin_origin").selectmenu("refresh");
     update_origin_active("edit_origin");
     $("#edit_origin_neworigin").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_forage_form").submit(function(){
    $("#edit_forage_form").on('submit', function(){
        var forage = $("#edit_forage_forage").val();
        var newforage = $("#edit_forage_newforage").val().toUpperCase();
        if (newforage == "") {
           newforage = forage;
        }
        $.ajax({
           type: "POST",
           url: "edit_forage.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Forage.");
   } else if (data == "success!") {
     showAlert("edit_forage_notification",
               "Forage successfully edited.");
     update_forage("edit_forage", false);
     $("#edit_forage_forage").val(newforage);
     $("#edit_forage_forage").selectmenu();
     $("#edit_forage_forage").selectmenu("refresh");
     update_edit_forage();
     $("#edit_forage_newforage").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_paddock_form").submit(function(){
    $("#edit_paddock_form").on('submit', function(){
        var paddock = $("#edit_paddock_paddock").val();
        var newpaddock = $("#edit_paddock_newpaddock").val().toUpperCase();
        if (newpaddock == "") {
           newpaddock = paddock;
        }
        $.ajax({
           type: "POST",
           url: "edit_paddock.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Paddock ID.");
   } else if (data == "success!") {
     showAlert("edit_paddock_notification",
               "Paddock successfully edited.");
     update_paddock("edit_paddock", false);
     $("#edit_paddock_paddock").val(newpaddock);
     $("#edit_paddock_paddock").selectmenu();
     $("#edit_paddock_paddock").selectmenu("refresh");
     update_edit_paddock();
     $("#edit_paddock_newpaddock").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_reason_form").submit(function(){
    $("#edit_reason_form").on('submit', function(){
        var reason = $("#edit_reason_reason").val();
        var newreason = $("#edit_reason_newreason").val().toUpperCase();
        if (newreason == "") {
           newreason = reason;
        }
        $.ajax({
           type: "POST",
           url: "edit_reason.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Reason.");
   } else if (data == "success!") {
     showAlert("edit_reason_notification",
               "Reason successfully edited.");
     update_reason("edit_reason", false);
     $("#edit_reason_reason").val(newreason);
     $("#edit_reason_reason").selectmenu();
     $("#edit_reason_reason").selectmenu("refresh");
     $("#edit_reason_newreason").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_med_form").submit(function(){
    $("#edit_med_form").on('submit', function(){
        var med = $("#edit_med_med").val();
        var newmed = $("#edit_med_newmed").val().toUpperCase();
        if (newmed == "") {
           newmed = med;
        }
        $.ajax({
           type: "POST",
           url: "edit_med.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Medication.");
   } else if (data == "success!") {
     showAlert("edit_med_notification",
               "Medication successfully edited.");
     update_med("edit_med", false);
     $("#edit_med_med").val(newmed);
     $("#edit_med_med").selectmenu();
     $("#edit_med_med").selectmenu("refresh");
     update_edit_med();
     $("#edit_med_newmed").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_wormer_form").submit(function(){
    $("#edit_wormer_form").on('submit', function(){
        var wormer = $("#edit_wormer_wormer").val();
        var newwormer = $("#edit_wormer_newwormer").val().toUpperCase();
        if (newwormer == "") {
           newwormer = wormer;
        }
        $.ajax({
           type: "POST",
           url: "edit_wormer.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Wormer.");
   } else if (data == "success!") {
     showAlert("edit_wormer_notification",
               "Wormer successfully edited.");
     update_wormer("edit_wormer", false);
     $("#edit_wormer_wormer").val(newwormer);
     $("#edit_wormer_wormer").selectmenu();
     $("#edit_wormer_wormer").selectmenu("refresh");
     $("#edit_wormer_newwormer").val("");
     update_edit_wormer();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_move_report_form").submit(function(){
    $("#edit_move_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "edit_move_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#edit_move_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_move_form").submit(function(){
    $("#edit_move_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "move_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Grazing move record successfully edited.");
     location.hash = "#edit_move_report";
     $("#edit_move_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#add_user_form").submit(function(){
    $("#add_user_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_user.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate username.");
   } else if (data == "success!") {
      showAlert("add_user_notification", 
                "User successfully added.");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#edit_user_form").submit(function(){
    $("#edit_user_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "user_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("User successfully edited.");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#notes_input_form").submit(function(){
    $("#notes_input_form").on('submit', function(){

       if (notes_validate()) {
  
          $.ajax({
              type: "POST",
              url: "notes_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("notes_notification", "Note successfully added.");
      clearForm("notes_file");
      $("#notes_note").val("");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    // $("#notes_report_form").submit(function(){
    $("#notes_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "notes_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#notes_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    // $("#notes_edit_form").submit(function(){
    $("#notes_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "notes_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Note successfully edited.");
     location.hash = "#notes_report";
     $("#notes_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#sale_input_form").on('submit', function(){

       if (sale_input_validate()) {
          $.ajax({
              type: "POST",
              url: "sale_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: This animal has already been sold.");
   } else if (data == "success!") {
      showAlert("sale_input_notification", "Sale record successfully added.");
      update_animal_id('sale_input', true, null);
      animal_update('sale_input');
      $("#sale_input_comments").val("");
      $("#sale_input_weight").val("");
      $("#sale_input_total_price").val("");
      $("#sale_input_net_price").val("");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#edit_dest_sale_form").on('submit', function(){
        var dest = $("#edit_dest_sale_dest").val();
        var newdest = $("#edit_dest_sale_newdest").val().toUpperCase();
        if (newdest == "") {
           newdest = dest;
        }
        $.ajax({
           type: "POST",
           url: "edit_dest_sale.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Sale Destination.");
   } else if (data == "success!") {
     showAlert("edit_dest_sale_notification",
               "Sale destination successfully edited.");
     update_dest("edit_dest_sale", false, false);
     $("#edit_dest_sale_dest").val(newdest);
     $("#edit_dest_sale_dest").selectmenu();
     $("#edit_dest_sale_dest").selectmenu("refresh");
     update_dest_sale_active("edit_dest_sale");
     $("#edit_dest_sale_newdest").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#sale_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "sale_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#sale_report_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#sale_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "sale_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: that animal has already been sold.");
   } else if (data == "success!") {
     alert("Sale record successfully edited.");
     location.hash = "#sale_report";
     $("#sale_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_slay_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_slayhouse.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate slaughter house.");
   } else if (data == "success!") {
      alert("Slaughter house successfully added.");
      location.hash = returnHash;
      update_slayhouse(slayhouseId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#slay_input_form").on('submit', function(){

       if (slay_input_validate()) {
          $.ajax({
              type: "POST",
              url: "slay_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: This animal has already been slaughtered.");
   } else if (data == "success!") {
      showAlert("slay_input_notification", "Slaughter record successfully added.");
      update_animal_id('slay_input', true, null);
      animal_update('slay_input');
      $("#slay_input_comments").val("");
      $("#slay_input_weight").val("");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#slay_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "slay_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#slay_report_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#slay_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "slay_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: that animal has already been slaughtered.");
   } else if (data == "success!") {
     alert("Slaughter record successfully edited.");
     location.hash = "#slay_report";
     $("#slay_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_slayhouse_form").on('submit', function(){
        var house = $("#edit_slayhouse_house").val();
        var newhouse = $("#edit_slayhouse_newhouse").val().toUpperCase();
        if (newhouse == "") {
           newhouse = house;
        }
        $.ajax({
           type: "POST",
           url: "edit_slayhouse.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate slaughter house.");
   } else if (data == "success!") {
     showAlert("edit_slayhouse_notification",
               "Slaughter house successfully edited.");
     update_slayhouse("edit_slayhouse", false, false);
     $("#edit_slayhouse_house").val(newhouse);
     $("#edit_slayhouse_house").selectmenu();
     $("#edit_slayhouse_house").selectmenu("refresh");
     update_slayhouse_active("edit_slayhouse");
     $("#edit_slayhouse_newhouse").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_other_dest_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_other_dest.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate destination.");
   } else if (data == "success!") {
      alert("Destination successfully added.");
      location.hash = returnHash;
      update_other_dest(otherDestId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_dest_other_form").on('submit', function(){
        var dest = $("#edit_dest_other_dest").val();
        var newdest = $("#edit_dest_other_newdest").val().toUpperCase();
        if (newdest == "") {
           newdest = dest;
        }
        $.ajax({
           type: "POST",
           url: "edit_dest_other.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Destination.");
   } else if (data == "success!") {
     showAlert("edit_dest_other_notification",
               "Other removal destination successfully edited.");
     update_other_dest("edit_dest_other", false, false);
     $("#edit_dest_other_dest").val(newdest);
     $("#edit_dest_other_dest").selectmenu();
     $("#edit_dest_other_dest").selectmenu("refresh");
     update_other_dest_active("edit_dest_other");
     $("#edit_dest_other_newdest").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_other_reason_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_other_reason.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate reason.");
   } else if (data == "success!") {
      alert("Reason successfully added.");
      location.hash = returnHash;
      update_other_reason(otherReasonId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_reason_other_form").on('submit', function(){
        var reason = $("#edit_reason_other_reason").val();
        var newreason = $("#edit_reason_other_newreason").val().toUpperCase();
        if (newreason == "") {
           newreason = reason;
        }
        $.ajax({
           type: "POST",
           url: "edit_reason_other.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate Reason.");
   } else if (data == "success!") {
     showAlert("edit_reason_other_notification",
               "Reason successfully edited.");
     update_other_reason("edit_reason_other", false, false);
     $("#edit_reason_other_reason").val(newreason);
     $("#edit_reason_other_reason").selectmenu();
     $("#edit_reason_other_reason").selectmenu("refresh");
     $("#edit_reason_other_newreason").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#other_input_form").on('submit', function(){

       if (other_input_validate()) {
          $.ajax({
              type: "POST",
              url: "other_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: This animal has already been removed.");
   } else if (data == "success!") {
      showAlert("other_input_notification", 
                "Other removal record successfully added.");
      update_animal_id('other_input', true, null);
      animal_update('other_input');
      $("#other_input_comments").val("");
      $("#other_input_wt").val("N/A");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#other_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "other_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#other_report_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#other_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "other_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: that animal has already been removed.");
   } else if (data == "success!") {
     alert("Other removal record successfully edited.");
     location.hash = "#other_report";
     $("#other_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_feed_type_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_feed_type.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate major feed type.");
   } else if (data == "success!") {
      alert("Major feed type successfully added.");
      location.hash = returnHash;
      update_feed_type(feed_typeId, true, false);
      update_feed_subtype(feed_subtypeId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_feed_subtype_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_feed_subtype.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate feed type detail.");
   } else if (data == "success!") {
      alert("Feed type details successfully added.");
      location.hash = returnHash;
      update_feed_subtype(feed_subtypeId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_vendor_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_vendor.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate vendor.");
   } else if (data == "success!") {
      alert("Vendor successfully added.");
      location.hash = returnHash;
      update_vendor(vendorId, true, false);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#add_feed_unit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_feed_unit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate feed unit.");
   } else if (data == "success!") {
      alert("Feed unit successfully added.");
      location.hash = returnHash;
      update_feed_unit(feed_unitId, true);
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#feed_input_form").on('submit', function(){

       if (feed_input_validate()) {
          $.ajax({
              type: "POST",
              url: "feed_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("feed_input_notification", 
                "Feed purchase record successfully added.");
      $("#feed_input_comments").val("");
      $("#feed_input_purchased").val("");
      $("#feed_input_price").val("");
      $("#feed_input_total").val("");
      $("#feed_input_unit_weight").val("");
      $("#feed_input_total_weight").val("");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#edit_feed_type_form").on('submit', function(){
        var feed_type = $("#edit_feed_type_type").val();
        var newfeed_type = $("#edit_feed_type_newfeed_type").val().toUpperCase();
        if (newfeed_type == "") {
           newfeed_type = feed_type;
        }
        $.ajax({
           type: "POST",
           url: "edit_feed_type.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate feed major type.");
   } else if (data == "success!") {
     showAlert("edit_feed_type_notification",
               "Feed major type successfully edited.");
     update_feed_type("edit_feed_type", false, false);
     $("#edit_feed_type_type").val(newfeed_type);
     $("#edit_feed_type_type").selectmenu();
     $("#edit_feed_type_type").selectmenu("refresh");
     $("#edit_feed_type_newfeed_type").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_feed_subtype_form").on('submit', function(){
        var feed_subtype = $("#edit_feed_subtype_subtype").val();
        var newfeed_subtype = $("#edit_feed_subtype_newfeed_subtype").val().toUpperCase();
        if (newfeed_subtype == "") {
           newfeed_subtype = feed_subtype;
        }
        $.ajax({
           type: "POST",
           url: "edit_feed_subtype.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate feed type details.");
   } else if (data == "success!") {
     showAlert("edit_feed_subtype_notification",
               "Feed type details successfully edited.");
     update_feed_subtype("edit_feed_subtype", false, false);
     $("#edit_feed_subtype_subtype").val(newfeed_subtype);
     $("#edit_feed_subtype_subtype").selectmenu();
     $("#edit_feed_subtype_subtype").selectmenu("refresh");
     $("#edit_feed_subtype_newfeed_subtype").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_feed_vendor_form").on('submit', function(){
        var vendor = $("#edit_vendor_vendor").val();
        var newvendor = $("#edit_vendor_newvendor").val().toUpperCase();
        if (newvendor == "") {
           newvendor = vendor;
        }
        $.ajax({
           type: "POST",
           url: "edit_vendor.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate vendor.");
   } else if (data == "success!") {
     showAlert("edit_vendor_notification",
               "Vendor successfully edited.");
     update_vendor("edit_vendor", false, false);
     $("#edit_vendor_vendor").val(newvendor);
     $("#edit_vendor_vendor").selectmenu();
     $("#edit_vendor_vendor").selectmenu("refresh");
     $("#edit_vendor_newvendor").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_feed_unit_form").on('submit', function(){
        var feed_unit = $("#edit_feed_unit_unit").val();
        var newfeed_unit = $("#edit_feed_unit_newunit").val().toUpperCase();
        if (newfeed_unit == "") {
           newfeed_unit = feed_unit;
        }
        $.ajax({
           type: "POST",
           url: "edit_feed_unit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate feed unit.");
   } else if (data == "success!") {
     showAlert("edit_feed_unit_notification",
               "Feed unit successfully edited.");
     update_feed_unit("edit_feed_unit", false, false);
     $("#edit_feed_unit_unit").val(newfeed_unit);
     $("#edit_feed_unit_unit").selectmenu();
     $("#edit_feed_unit_unit").selectmenu("refresh");
     $("#edit_feed_unit_newunit").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#feed_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "feed_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#feed_report_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#feed_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "feed_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Feed purchase record successfully edited.");
     location.hash = "#feed_report";
     $("#feed_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#herd_health_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "health_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#herd_health_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#herd_prod_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "prod_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#herd_prod_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#daily_task_form").on('submit', function(){

       if (daily_task_validate()) {
          $.ajax({
              type: "POST",
              url: "daily_task_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      alert("Daily task list successfully added/edited.");
      update_daily_task_list();
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#add_task_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "add_task.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate task.");
   } else if (data == "success!") {
      alert("Task successfully added.");
      if (taskId == "daily_task") {
         daily_task_vals();
      } else {
         load_recur_task();
      }
      location.hash = returnHash;
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#edit_task_form").on('submit', function(){
        var task = $("#edit_task_task").val();
        var newtask = $("#edit_task_newtask").val().toUpperCase();
        if (newtask == "") {
           newtask = task;
        }
        $.ajax({
           type: "POST",
           url: "edit_task.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (
     data.startsWith("SQLSTATE[23000]: Integrity constraint violation: 1062")) {
      alert("Error: Duplicate task.");
   } else if (data == "success!") {
     showAlert("edit_task_notification",
               "Task successfully edited.");
     update_task("edit_task", false);
     $("#edit_task_task").val(newtask);
     $("#edit_task_task").selectmenu();
     $("#edit_task_task").selectmenu("refresh");
     update_edit_task();
     $("#edit_task_newtask").val("");
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#labor_report_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "labor_report.php",
           cache: false,
           data: new FormData(this),
           success: 
function (data, status) {
   $("#labor_table").html(data);
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#labor_input_form").on('submit', function(){

       if (labor_input_validate()) {
          $.ajax({
              type: "POST",
              url: "labor_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      showAlert("labor_input_notification", 
                "Labor record successfully added.");
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#labor_edit_form").on('submit', function(){
        $.ajax({
           type: "POST",
           url: "labor_edit.php",
           cache: false,
           data: new FormData(this),
           success: 
function(data, status) {
   if (data == "success!") {
     alert("Labor record successfully edited.");
     location.hash = "#notes_tasks_report";
     $("#labor_report_form").submit();
   } else {
      alert(data);
   }
},
           contentType: false,
           processData: false,
           error: onError
       });
       return false;
    });

    $("#recur_task_form").on('submit', function(){

       if (recur_task_validate()) {
          $.ajax({
              type: "POST",
              url: "recur_task_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      alert("Recurring task list successfully created/edited.");
      load_recur_task();
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

    $("#daily_chores_form").on('submit', function(){

       if (daily_chores_validate()) {
          $.ajax({
              type: "POST",
              url: "daily_chores_input.php",
              cache: false,
              data: new FormData(this),
              success: 
function(data, status) {
   if (data == "success!") {
      alert("Daily task list successfully updated.");
      update_daily_task_table();
   } else {
      alert(data);
   }
},
              contentType: false,
              processData: false,
              error: onError
          });
       }
  
       return false;
    });

});
