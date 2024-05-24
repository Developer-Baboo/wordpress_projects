<?php 
/**
* Plugin Name: Custom EMS
* Plugin URI: https://www.your-site.com/
* Description: My Plugin to explain curd functionality
* Version: 0.1
* Author: Baboo Kumar
* Author URI: https://www.your-site.com/
**/

register_activation_hook(__FILE__, 'table_creator');

function table_creator(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix. "ems";
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
                id mediumint(11) NOT NULL AUTO_INCREMENT,
                emp_id varchar(255) NOT NULL,
                emp_name varchar(255) NOT NULL,
                emp_email varchar(255) NOT NULL,
                emp_dept varchar(255) NOT NULL,
                PRIMARY KEY id(id)
            )$charset_collate;";
    require_once( ABSPATH. 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

add_action('admin_menu', 'da_display_esm_menu');

/* function da_display_esm_menu(){
    add_menu_page('EMS', 'EMS','manage_options', 'ems', 'emp-list', 'da_ems_list_callback');
} */
function da_display_esm_menu(){
    add_menu_page(
        'EMS', // Page title
        'EMS', // Menu title
        'manage_options', // Capability
        'ems', // Menu slug
        'da_ems_list_callback' // Function
    );

    add_submenu_page(
        'ems', // Parent slug
        'Employee List', // Page title
        'Employee List', // Menu title
        'manage_options', // Capability
        'emp-list', // Menu slug
        'da_ems_list_callback' // Function
    );

    add_submenu_page(
        'ems', // Parent slug
        'Add Employee', // Page title
        'Add Employee', // Menu title
        'manage_options', // Capability
        'add-emp', // Menu slug
        'da_ems_add_callback' // Function
    );

    add_submenu_page(
        null, // Parent slug
        'Update Employee', // Page title
        'Update Employee', // Menu title
        'manage_options', // Capability
        'update-emp', // Menu slug
        'da_ems_update_callback' // Function
    );
    
    add_submenu_page(
        null, // Parent slug
        'Delete Employee', // Page title
        'Delete Employee', // Menu title
        'manage_options', // Capability
        'delete-emp', // Menu slug
        'da_ems_delete_callback' // Function
    );
}
function da_ems_add_callback(){
    ?>
    <h1>Add Employee</h1>
    <form method="post" action="">
        <table>
        <tr>
                <td><label for="emp_id">Employee Id:</label></td>
                <td><input type="text" name="emp_id" id="emp_id" required></td>
            </tr>
            <tr>
                <td><label for="emp_name">Employee Name:</label></td>
                <td><input type="text" name="emp_name" id="emp_name" required></td>
            </tr>
            <tr>
                <td><label for="emp_email">Employee Email:</label></td>
                <td><input type="email" name="emp_email" id="emp_email" required></td>
            </tr>
            <tr>
                <td><label for="emp_dept">Employee Department:</label></td>
                <td><input type="text" name="emp_dept" id="emp_dept" required></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit" value="Add Employee"></td>
            </tr>
        </table>
    </form>
    <?php
}
// Processing form data
if(isset($_POST['submit'])){
    // Check if form fields are set and not empty
    if(isset($_POST['emp_id']) && isset($_POST['emp_name']) && isset($_POST['emp_email']) && isset($_POST['emp_dept']) && !empty($_POST['emp_id']) && !empty($_POST['emp_name']) && !empty($_POST['emp_email']) && !empty($_POST['emp_dept'])) {
        // Add your code to insert data into the database here
        global $wpdb;
        $msg = '';
        // $table_name = $wpdb->prefix . "ems";

        $table_name = "wp_ems";
        $emp_id = $_POST['emp_id'];
        $emp_name = $_POST['emp_name'];
        $emp_email = $_POST['emp_email'];
        $emp_dept = $_POST['emp_dept'];

        $wpdb->insert($table_name, array(
            'emp_id' => $emp_id,
            'emp_name' => $emp_name,
            'emp_email' => $emp_email,
            'emp_dept' => $emp_dept
        ));

        if($wpdb->insert_id > 0){
            $msg = "Employee added successfully";
        } else {
            $msg = "Error while adding employee";
        }
        echo $msg;
        
    } 
}
function da_ems_list_callback(){
    global $wpdb;
    $table_name = "wp_ems";

    // Fetch data from the database
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Check if there are any results
    if($results) {
        echo "<h1>Employee List</h1>";
        echo "<style>";
        echo "table { border-collapse: collapse; }"; // Collapse borders
        echo "th, td { padding: 8px; }"; // Add padding to cells
        echo "</style>";
        echo "<table border='2'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Employee ID</th>";
        echo "<th>Employee Name</th>";
        echo "<th>Employee Email</th>";
        echo "<th>Employee Department</th>";
        echo "<th colspan='2'>Action</th>"; // Colspan for Action column
        echo "</tr>";

        // Loop through each row of the results
        foreach($results as $row) {
            echo "<tr>";
            echo "<td>{$row->id}</td>";
            echo "<td>{$row->emp_id}</td>";
            echo "<td>{$row->emp_name}</td>";
            echo "<td>{$row->emp_email}</td>";
            echo "<td>{$row->emp_dept}</td>";
            echo "<td><a href='admin.php?page=update-emp&id={$row->id}'>Edit</a></td>"; // Edit link with ID
            echo "<td><a href='admin.php?page=delete-emp&id={$row->id}'>Delete</a></td>"; // Delete link with ID
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No employees found.</p>";
    }

}

function da_ems_update_callback(){
    global $wpdb;

    // Check if form is submitted for update
    if(isset($_POST['update'])){
        // Retrieve form data
        $id = isset($_POST['id']) ? intval($_POST['id']) : "";
        $emp_id = isset($_POST['emp_id']) ? sanitize_text_field($_POST['emp_id']) : "";
        $emp_name = isset($_POST['emp_name']) ? sanitize_text_field($_POST['emp_name']) : "";
        $emp_email = isset($_POST['emp_email']) ? sanitize_email($_POST['emp_email']) : "";
        $emp_dept = isset($_POST['emp_dept']) ? sanitize_text_field($_POST['emp_dept']) : "";

        // Check if form fields are set and not empty
        if(!empty($id) && !empty($emp_id) && !empty($emp_name) && !empty($emp_email) && !empty($emp_dept)) {
            // Update employee data in the database
            $table_name = $wpdb->prefix . "ems";
            $result = $wpdb->update(
                $table_name,
                array(
                    'emp_id' => $emp_id,
                    'emp_name' => $emp_name,
                    'emp_email' => $emp_email,
                    'emp_dept' => $emp_dept
                ),
                array('id' => $id),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );

            // Check if update was successful
            if($result !== false) {
                echo "<p>Employee updated successfully!</p>";
            } else {
                echo "<p>Error updating employee!</p>";
            }
        } else {
            echo "<p>Please fill in all fields!</p>";
        }
    }

    // Fetch employee data by ID and render the update form
    if(isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Fetch employee data by ID
        $table_name = $wpdb->prefix . "ems";
        $employee = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        // Check if employee exists
        if($employee) {
            ?>
            <h1>Edit Employee</h1>
            <form method="post" action="">
                <table>
                    <tr>
                        <td><label for="emp_id">Employee Id:</label></td>
                        <td><input type="text" name="emp_id" id="emp_id" value="<?php echo esc_attr($employee->emp_id); ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="emp_name">Employee Name:</label></td>
                        <td><input type="text" name="emp_name" id="emp_name" value="<?php echo esc_attr($employee->emp_name); ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="emp_email">Employee Email:</label></td>
                        <td><input type="email" name="emp_email" id="emp_email" value="<?php echo esc_attr($employee->emp_email); ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="emp_dept">Employee Department:</label></td>
                        <td><input type="text" name="emp_dept" id="emp_dept" value="<?php echo esc_attr($employee->emp_dept); ?>" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" name="update" value="Update Employee"></td>
                    </tr>
                </table>
            </form>
            <?php
        } else {
            echo "<p>Employee not found!</p>";
        }
    } else {
        echo "<p>No employee ID provided!</p>";
    }
}



function da_ems_delete_callback(){
    global $wpdb;

    // Check if ID parameter is set in the URL
    if(isset($_GET['id'])) {
        $id = $_GET['id'];

        // Check if employee exists before deleting
        $table_name = "wp_ems";
        $employee = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if($employee) {
            // Attempt to delete the employee record
            $result = $wpdb->delete(
                $table_name,
                array('id' => $id),
                array('%d')
            );

            // Check if deletion was successful
            if($result !== false) {
                // echo "<p>Employee deleted successfully!</p>";
            } else {
                echo "<p>Error deleting employee!</p>";
            }
        } else {
            echo "<p>Employee not found!</p>";
        }
    } else {
        echo "<p>No employee ID provided!</p>";
    }
}




?>