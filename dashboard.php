<?php
session_start(); // Start the session

// Check if the user is logged in (i.e., the username is set in the session)
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Retrieve the username from session
    echo "Welcome, " . htmlspecialchars($username) . "!";
} else {
    // If no username is found in the session, redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <title>Table View UI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
            display: flex;
        }
        .menu-bar {
            position: fixed;
            left: 0;
            top: 0;
		
            width: 180px;
            height: 100%;
            background-color: #495057;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }
        .menu-bar:hover {
            background-color: #343a40;
        }
        .menu-bar h2 {
            margin-top: 10;
            font-size: 20px;
            transition: color 0.3s ease;
        }
        .menu-bar a {
            color: white;
            text-decoration: none;
            margin: 15px 0;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .menu-bar a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .title-bar {
            position: fixed;
            top: 0;
            left: 0px;
            right: 0;
            height: 80px;
            background-color:  #d9534f;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }
        
        .content {
            margin-left: 200px;
		
            margin-top: 60px;
            padding: 20px;
            width: calc(100% - 220px);
        }
        .container {
            max-width: 1200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .header button:hover {
            background-color: #c9302c;
            transform: scale(1.05);
        }
        .section-title {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #d9534f;
            transition: color 0.3s ease;
        }
        .table-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            margin-top: 10px;
		
        }
        .table-item {
            background-color: #e0e0e0;
            border: 1px dashed #bdbdbd;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .table-item:hover {
            background-color: #ffc107;
            transform: translateY(-5px);
        }
        /* Full-screen Popup Styling */
        .popup {
            position: fixed;
            top: 0;
            left: 0px;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background-color: white;
            width: 90%;
            height: 90%;
            border-radius: 8px;
            overflow: auto;
            position: relative;
            padding: 20px;
        }
        .close-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
		
		
		
		
		.settings-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .settings-popup-content {
            background-color: white;
            width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
		
		
		 /* Style for buttons inside popup */
        .popup .button-grid {

            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            height: 100%;
        }

        .popup .button-grid button {
            background: #d9534f;
			height: 100px;
			width:200px;
            color: white;
            border: none;
            padding: 20px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            border-radius: 5px;
            transition: background 0.3s, transform 0.3s;
        }

        .popup .button-grid button:hover {
            
            transform: scale(1.1);
        }

        /* Overlay for background */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 500;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .overlay.show {
            display: block;
            opacity: 1;
        }

		 /* Style for second popup content */
        .second-popup-content {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }

/* Basic popup styling with transition */
        .alert-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0; /* Hidden initially */
            pointer-events: none; /* Disable clicks when hidden */
            transition: opacity 0.4s ease;
            z-index: 1000;
        }
        .alert-box.visible {
            opacity: 1; /* Show on visibility */
            pointer-events: auto; /* Enable clicks */
        }
        .alert-content {
            background: white;
            padding: 20px;
            width: 400px;
            max-height: 70%;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            font-family: Arial, sans-serif;
        }
          /* Table styling */
        #itemTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        #itemTable th, #itemTable td {
            border: 1px solid #ccc; /* Border for table cells */
            padding: 10px; /* Cell padding */
            text-align: left; /* Align text to the left */
        }
        #itemTable th {
            background-color: #f2f2f2; /* Light gray background for headers */
            font-weight: bold; /* Bold font for headers */
        }
        #itemTable tr:nth-child(even) {
            background-color: #f9f9f9; /* Light background for even rows */
        }
        button {
            margin: 10px 5px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #ddd;
        }

        .notification-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    padding: 10px;
}

.notification {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}


		
    </style>
</head>
<body>
    <div class="menu-bar">
        <h2>Menu</h2>
        <a href="#">Dashboard</a>
        <a href="#">Reservations</a>
        <a href="#" >Orders</a>
        <a href="#">Tables</a>
        <a href="#" onclick="openPopup()" >Settings</a>
    </div>
	
	
	
	<!-- HTML element to store username -->
    <div id="username" data-username="<?php echo htmlspecialchars($username); ?>">
        Welcome, <span id="display-username"><?php echo htmlspecialchars($username); ?></span>!
    </div>

	
	
    <div class="title-bar">
        <h1>Table View UI</h1>
    </div>
    <div class="content">
        <div class="container" id="tableContainer">
            <div class="header">
                <div>
                    <button>Table Reservation</button>
                    <button>Contactless</button>
                </div>
                <div>
                    <button>Delivery</button>
                    <button>Pick Up</button>
                     <button onclick="openSettingsPopup()">Add Table</button> <!-- Open Settings Popup -->

                </div>
            </div>
			
			
			<div class="section-title">Outdoor</div>
            <div class="table-grid" data-section="Outdoor">
                <div class="table-item" onclick="openPopup(this,'Outdoor')">1</div>
                <div class="table-item" onclick="openPopup(this,'Outdoor')">2</div>
                <div class="table-item" onclick="openPopup(this,'Outdoor')">3</div>
                <div class="table-item" onclick="openPopup(this,'Outdoor')">4</div>
                <div class="table-item" onclick="openPopup(this,'Outdoor')">5</div>
            </div>
            
			
			<div class="section-title">Indoor</div>
            <div class="table-grid" data-section="Indoor">
                <div class="table-item" onclick="openPopup(this,'indoor')">1</div>
                <div class="table-item" onclick="openPopup(this,'indoor')">2</div>
                <div class="table-item" onclick="openPopup(this,'indoor')">3</div>
                <div class="table-item" onclick="openPopup(this,'indoor')">4</div>
                <div class="table-item" onclick="openPopup(this,'indoor')">5</div>
            </div>
			
			<div class="section-title">Ground</div>
            <div class="table-grid" data-section="Ground" >
                <div class="table-item" onclick="openPopup(this,'ground')">1</div>
                <div class="table-item" onclick="openPopup(this,'ground')">2</div>
                <div class="table-item" onclick="openPopup(this,'ground')">3</div>
                <div class="table-item" onclick="openPopup(this,'ground')">4</div>
                <div class="table-item" onclick="openPopup(this,'ground')">5</div>

				
				
            </div>
			
			
        </div>
    </div>
<!-- Full-screen Popup -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <button class="close-button" onclick="closePopup()">Close</button>
            <div class="container">
                <h2>Table Details</h2>
           
				
			<div class="button-grid">
            <button onclick="openSecondPopup(1)">ADD FOOD</button>
            <button onclick="openSecondPopup(2)">RECEIPT SETTING</button>
            <button onclick="openSecondPopup(3)">Button 3</button>
            <button onclick="openSecondPopup(4)">Button 4</button>
            <button onclick="openSecondPopup(5)">Button 5</button>
            <button onclick="openSecondPopup(6)">Button 6</button>
            <button onclick="openSecondPopup(7)">Button 7</button>
            <button onclick="openSecondPopup(8)">Button 8</button>
            <button onclick="openSecondPopup(9)">Button 9</button>
        </div>
				
				
            </div>
        </div>
    </div>
	
	<!-- Second Popup -->
<div class="popup" id="second-popup">
    <div class="popup-content">
	 <div class="container">
        <button class="close-button" onclick="closeSecondPopup()">Close</button>
        <div id="popupContent">
            <!-- Content will be dynamically updated here -->
        </div>
    </div>
</div>
 </div>
    
 <!-- Settings Popup -->

<div class="settings-popup" id="settingsPopup">
    <div class="settings-popup-content">
        <h2>Add Table</h2>
        <div class="form-group">
            <label for="sectionSelect">Select Section:</label>
            <select id="sectionSelect" onchange="updateNewSectionField()">
                <option value="">-- Select an Existing Section --</option>
                <option value="Outdoor">Outdoor</option>
                <option value="Indoor">Indoor</option>
                <option value="Ground">Ground</option>
            </select>
        </div>
        <div class="form-group">
            <label for="newSectionName">Or Enter New Section Name:</label>
            <input type="text" id="newSectionName" placeholder="Enter new section name" oninput="clearSectionSelect()">
        </div>
        <div class="form-group">
            <label for="numberOfTables">Number of Tables:</label>
            <input type="number" id="numberOfTables" placeholder="Enter number of tables" min="1">
        </div>
        <button class="submit-button" onclick="createTables()">Create Tables</button>
        <button class="close-button" onclick="closeSettingsPopup()">Close</button>
    </div>
</div>

<!-- Audio element to play notification sound -->
    <audio id="notificationSound" src="notification.mp3" preload="auto"></audio>


<div id="alerts"></div>

        <div id="alert-box" class="alert-box hidden">
            <div class="alert-content">

                <h2>Item Details</h2>
                <p id="file-name"></p>
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>

                        </tr>
                    </thead>
                    <tbody id="itemBody"></tbody>
                </table>

                <button id= "printButton" onclick="printFile()">Print</button>
                <button id = "rejectButton" onclick="closeAlert()">Reject</button>
            </div>
        </div>

   <div id="notification-container" class="notification-container">
    <!-- New notifications will be added here -->
</div>


    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>



    <script>
	
	var usernameFromPHP = "<?php echo htmlspecialchars($username); ?>";
        
        // Use the username in JavaScript
        console.log("Logged in as: " + usernameFromPHP);

    var ws = new WebSocket("ws://localhost:9999");

        ws.onopen = function() {
            console.log("Connected to WebSocket server.");
        };

const updateQueue = []; // Queue to hold updates
let isPopupVisible = false; // Flag to track popup visibility


	let table_name1='';

     ws.onmessage = function(event) {
		 
		 
	
		console.log(event.data)
		
		//const data = event.data
			
		const data = JSON.parse(event.data);
	
	if(data.hotel_name == usernameFromPHP)
	{
    updateQueue.push(data);
	
	console.log("table name from external user ",data.Table)

  
// Create a new notification element
const notification = document.createElement('div');
notification.className = 'notification';

// Create title for the notification
const alertTitle = document.createElement('h3');
alertTitle.innerText = `Table: ${data.Table}`;
notification.appendChild(alertTitle);


// Initialize an object to hold totals for each item
const totals = {};

// Process parsed content to calculate totals
  for (let itemKey in data.message) {
		console.log("checking for key ",itemKey)

        const itemData = data.message[itemKey];
		
		console.log(itemData.name)
		console.log(itemData.name.name)
        
        // Initialize the item entry if it doesn't exist in totals
        if (!totals[itemData.name]) {
            totals[itemData.name] = { totalQuantity: 0, totalPrice: 0 };
        }

        // Accumulate total quantity and total price for each item
        totals[itemData.name].totalQuantity += itemData.quantity;
        totals[itemData.name].totalPrice += itemData.price * itemData.quantity;
    }


// Create the item table
const itemTable = document.createElement('table');
const itemBody = document.createElement('tbody');
itemTable.appendChild(itemBody);

// Create table header for clarity
const itemHeader = document.createElement('thead');
itemHeader.innerHTML = `
    <tr>
        <th>Item Name</th>
        <th>Total Quantity</th>
        <th>Total Price</th>
    </tr>
`;
itemTable.appendChild(itemHeader);

// Add rows for each item and its totals
for (const itemName in totals) {
    if (totals.hasOwnProperty(itemName)) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${itemName}</td>
            <td>${totals[itemName].totalQuantity}</td>
            <td>${totals[itemName].totalPrice.toFixed(2)}</td> <!-- Format price with 2 decimals -->
        `;
        itemBody.appendChild(row);
    }
}

// Append the table to the notification
notification.appendChild(itemTable);

// Append the notification to the container
document.getElementById('notification-container').appendChild(notification);
    // Add buttons for the notification
    const rejectButton = document.createElement('button');
    rejectButton.innerText = "Reject";
    rejectButton.onclick = function () {
        console.log("Order rejected");
        notification.remove(); // Remove the notification from the grid





			const data = {
						table_name: "table_name1",

					};

        fetch('/order_reject', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json'
								},
								body: JSON.stringify(data)  // Convert data to JSON string
							})
							.then(response => {
								if (!response.ok) {
									throw new Error(`HTTP error! Status: ${response.status}`);
								}
								return response.json();
							})
							.then(responseData => {
								console.log("Server response:", responseData.message);
							})
							.catch(error => {
								console.error("Error saving data:", error);
							});


    };
    notification.appendChild(rejectButton);

    const printButton = document.createElement('button');
    printButton.innerText = "Print";
    printButton.onclick = function () {
        console.log("Order printed");
        notification.remove(); // Remove the notification from the grid



        const data = {
						table_name: "table_name1",

					};

        fetch('/order_accept', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json'
								},
								body: JSON.stringify(data)  // Convert data to JSON string
							})
							.then(response => {
								if (!response.ok) {
									throw new Error(`HTTP error! Status: ${response.status}`);
								}
								return response.json();
							})
							.then(responseData => {
								console.log("Server response:", responseData.message);
							})
							.catch(error => {
								console.error("Error saving data:", error);
							});

    };
    notification.appendChild(printButton);
};

	 }
function showNextPopup() {

    const data = updateQueue.shift(); // Get the next update from the queue
    isPopupVisible = true; // Set the flag to indicate popup is visible

    console.log("queue data ",updateQueue.length)
    console.log("queue data1 ",data)
    console.log("new table name ",data.filename)



   // Update the popup content with the new data
    const alertBox = document.getElementById('alert-box');
    const alertTitle = document.getElementById('file-name').innerText = `Table : ${data.filename}`;


    const parsedContent = JSON.parse(data.content);
    const totals = {}; // Create a map to hold total price and quantity for each item

    for (const itemKey in parsedContent) {
        if (parsedContent.hasOwnProperty(itemKey)) {
            const itemData = parsedContent[itemKey];
            if (!totals[itemData.name]) {
                totals[itemData.name] = { totalQuantity: 0, totalPrice: 0 };
            }
            totals[itemData.name].totalQuantity += itemData.quantity;
            totals[itemData.name].totalPrice += itemData.Price * itemData.quantity; // price * quantity
        }
    }

    // Display totals in the table
    const itemBody = document.getElementById('itemBody');
    itemBody.innerHTML = ""; // Clear previous content
    for (const itemName in totals) {
        if (totals.hasOwnProperty(itemName)) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${itemName}</td>
                <td>${totals[itemName].totalQuantity}</td>
            `;
            itemBody.appendChild(row);
        }
    }




    alertBox.classList.add('visible');



    // Handle the reject button click
    document.getElementById('rejectButton').onclick = function () {
        console.log("Order rejected");

        dismissPopup(); // Dismiss the popup
    };

    // Handle the print button click
    document.getElementById('printButton').onclick = function () {
        console.log("Order printed");
        // Implement your print logic here
        dismissPopup(); // Dismiss the popup
    };
}

// Function to dismiss the popup and show the next one in the queue
function dismissPopup() {
    const alertBox = document.getElementById('alert-box');
    alertBox.classList.remove('visible');
    isPopupVisible = false;
    showNextPopup(); // Show the next popup in the queue
}


        function printFile() {
            window.print()
            closeAlert(); // Close the popup after action();
        }

        function closeAlert() {
     document.getElementById('alert-box').classList.remove('visible'); // Hide the popup
}





   
	 // Establish connection to the server's SSE endpoint
        const eventSource = new EventSource('/alerts');

        // Listen for messages and show them in an alert popup
        eventSource.onmessage = function(event) {
            //const alertsDiv = document.getElementById('alerts');
            //const newAlert = document.createElement('p');
            //newAlert.textContent = event.data;
            //alertsDiv.appendChild(newAlert);

            // Show an alert popup with the received message
            alert(event.data); // This will display a popup alert with the message
        };

        eventSource.onerror = function() {
            console.error("Error connecting to SSE.");
            eventSource.close();
        };
	
	
	
	
		

	
	function openPopup(buttonElement, section) {
	
	document.getElementById('popup').style.display = 'flex';
    const tableNumber = buttonElement.innerText; // Get the table number
    const tableKey = `${section}_table_${tableNumber}`; // Create a unique key for each section and table
    localStorage.setItem('currentTable', tableKey);
    window.location.href = "backendpage.html";
}

	
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
		
		function closeSecondPopup() {
        console.log("Closing second popup");
        var secondPopup = document.getElementById("second-popup");
        if (secondPopup) {
            secondPopup.style.display = "none"; // Hide the second popup
            console.log("Second popup closed");
        } else {
            console.log("Second popup element not found");
        }
    }
		
		
		

	
    function closeSettingsPopup() {
        document.getElementById('settingsPopup').style.display = 'none';
    }



// Function to open the second popup
       function openSecondPopup(buttonNumber) {
    console.log("Button " + buttonNumber + " clicked");
    var secondPopup = document.getElementById("second-popup");
    if (secondPopup) {
        secondPopup.style.display = "block";
        console.log("Second popup should be visible now");
    } else {
        console.log("Second popup element not found");
    }
}


function openSecondPopup(buttonNumber) {
        var secondPopup = document.getElementById("second-popup");
        var popupContent = document.getElementById("popupContent");

        if (secondPopup) {
            // Show the second popup
            secondPopup.style.display = "block";

            // Increase the z-index of the second popup to be on top
            secondPopup.style.zIndex = 1000; // Ensure it’s higher than the first popup

            // Clear previous content
            popupContent.innerHTML = ""; 

            // Determine which button was clicked and update the content
            switch (buttonNumber) {
                case 1:
                    popupContent.innerHTML = `
                        <h3>Upload Food Details</h3>
                        <p>Please upload the food details file (CSV, Excel, etc.).</p>
						<div style="border: 1px solid #007BFF; border-radius: 8px; padding: 15px; background-color: #f8f9fa; color: #343a40; margin: 20px 0;">
							<h3 style="color: #007BFF;">How to Upload Your Excel File</h3>
							<p style="line-height: 1.6;">
								To upload your Excel file, please follow these simple steps:
							</p>
							<ul style="margin: 10px 0; padding-left: 20px;">
								<li><strong>Step 1:</strong> Click on the "Choose File" button to select the Excel file you wish to upload.</li>
								<li><strong>Step 2:</strong> Ensure that your file is in .xlsx or .xls format. Other formats may not be accepted.</li>
								<li><strong>Step 3:</strong> After selecting the file, click the "Upload" button to begin the upload process.</li>
								<li><strong>Step 4:</strong> Wait for a confirmation message indicating that your file has been uploaded successfully.</li>
							</ul>
							<p style="line-height: 1.6;">
								If you encounter any issues, please check the file format and size. For assistance, feel free to reach out to our support team.
							</p>
						</div>

                        <form id="uploadForm" enctype="multipart/form-data">
                            <input type="file" id="fileInput" name="file" accept=".csv, .xlsx" required>
                            <button type="submit">Upload</button>
                        </form>
                        <p id="uploadMessage"></p>
                    `;
                    handleUpload(); // Attach the upload handler
                    break;

                case 2:
                    popupContent.innerHTML = `
                        <h3>Receipt Settings</h3>
                        <p>Configure your receipt settings here.</p>
                        <form id="receiptSettingsForm">
                            <label for="setting1">Setting 1:</label>
                            <input type="text" id="setting1" name="setting1" required>
                            <button type="submit">Save Settings</button>
                        </form>
                        <p id="settingsMessage"></p>
                    `;
                    handleReceiptSettings(); // Attach the settings handler
                    break;

                default:
                    popupContent.innerHTML = `<p>No content available for this option.</p>`;
            }
        }
    }
	
	
	function openSettingsPopup() {
        document.getElementById('settingsPopup').style.display = 'flex';
    }


    function closeSecondPopup() {
        console.log("Closing second popup");
        var secondPopup = document.getElementById("second-popup");
        if (secondPopup) {
            secondPopup.style.display = "none"; // Hide the second popup
            console.log("Second popup closed");
        } else {
            console.log("Second popup element not found");
        }
    }

    function closePopup() {
        var popup = document.getElementById("popup");
        if (popup) {
            popup.style.display = "none"; // Hide the first popup
        }
    }

    function handleUpload() {
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            var fileInput = document.getElementById('fileInput');
            var uploadMessage = document.getElementById('uploadMessage');

            if (fileInput.files.length > 0) {
                var file = fileInput.files[0];

                // Read the file using FileReader
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Parse the Excel file
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, { type: 'array' });
                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    var jsonData = XLSX.utils.sheet_to_json(firstSheet);

                    // Store the data in local storage
                    localStorage.setItem('uploadedData', JSON.stringify(jsonData));

                    // Display a message indicating that the file is uploaded
                    uploadMessage.textContent = 'File "' + file.name + '" uploaded successfully! Data stored in local storage.';					
                };
                reader.readAsArrayBuffer(file);
				
		// Step 1: Retrieve data from localStorage
		
		const datafromlocalstorage =  JSON.parse(localStorage.getItem('uploadedData'));
	
	
	
      try {
		  
		  
		  
						  // Retrieve the data from localStorage
				const datafromlocalstorage = JSON.parse(localStorage.getItem('uploadedData'));

				// Assuming 'uploadedData' is an array or object that contains menu data
				console.log(datafromlocalstorage); // Check the data structure

				// Send the data to PHP via AJAX
				if (datafromlocalstorage) {
					fetch('/code/store_menu.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify({ menu: datafromlocalstorage })
					})
					.then(response => response.json())
					.then(data => {
						console.log("server resp php ",data)
						if (data.success) {
							alert('Menu saved successfully!');
						} else {
							alert('Error saving menu');
						}
					})
					.catch(error => {
						console.error('Error:', error);
						alert('Error sending data to server');
					});
				}

		  
		  
        
      } catch (error) {
       

	   }
	
				
				
				
				
				
				
            } else {
                uploadMessage.textContent = 'Please select a file to upload.';
            }
        });
    }


    function handleReceiptSettings() {
        document.getElementById('receiptSettingsForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            var setting1 = document.getElementById('setting1').value;
            var settingsMessage = document.getElementById('settingsMessage');

            // Display a message indicating that the settings have been saved
            settingsMessage.textContent = 'Settings saved: ' + setting1;
        });
    }

    // Close popup with Esc key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSecondPopup();
        }
    });



 
 function updateNewSectionField() {
    // Clear the new section name input if a section is selected from the dropdown
    document.getElementById('newSectionName').value = '';
}

function clearSectionSelect() {
    // Clear the section select dropdown if the user starts typing in the new section name input
    document.getElementById('sectionSelect').selectedIndex = 0;
}





function createTables() {
    const existingSection = document.getElementById('sectionSelect').value; // Get the selected value from the dropdown
    const newSectionName = document.getElementById('newSectionName').value.trim(); // Get the value from the new section input
    const numberOfTables = parseInt(document.getElementById('numberOfTables').value);

    let sectionName;
    
    // Determine if the user chose an existing section or created a new one
    if (existingSection) {
        sectionName = existingSection; // Use the existing section
		
		// Store the new section in localStorage
        let sections = JSON.parse(localStorage.getItem('sections')) || [];
        if (!sections.includes(sectionName)) {
            sections.push(sectionName);
            localStorage.setItem('sections', JSON.stringify(sections));
        }
	
		
    } else if (newSectionName) {
        sectionName = newSectionName.charAt(0).toUpperCase() + newSectionName.slice(1); // Capitalize the first letter
    
	 // Store the new section in localStorage
        let sections = JSON.parse(localStorage.getItem('sections')) || [];
        if (!sections.includes(sectionName)) {
            sections.push(sectionName);
            localStorage.setItem('sections', JSON.stringify(sections));
        }
	
	}

    if (sectionName && numberOfTables > 0) {
        // Check if the section exists in the HTML
        const section = document.querySelector(`.table-grid[data-section="${sectionName}"]`);

        if (!section) {
            // If the section does not exist, create a new section in the HTML
            const newSectionDiv = document.createElement('div');
			
            newSectionDiv.className = 'table-grid';
            newSectionDiv.setAttribute('data-section', sectionName);
            newSectionDiv.innerHTML = `<div class="section-title">${sectionName}</div>`;
			
            document.getElementById('tableContainer').appendChild(newSectionDiv);
        }
		
		
		
		
		

        // Add the tables to the section
        const targetSection = document.querySelector(`.table-grid[data-section="${sectionName}"]`);
        for (let i = 0; i < numberOfTables; i++) {
            const tableNumber = targetSection.children.length+1; // Get next table number
            const newTable = document.createElement('div');
            newTable.className = 'table-item';
            newTable.innerText = tableNumber;
            newTable.onclick = function() { openPopup(this, sectionName); };
            targetSection.appendChild(newTable);

            // Store the created table in localStorage
            const tables = JSON.parse(localStorage.getItem(sectionName)) || [];
            tables.push(tableNumber);
            localStorage.setItem(sectionName, JSON.stringify(tables));
        }
        closeSettingsPopup(); // Close the settings popup
        document.getElementById('numberOfTables').value = ''; // Clear input fields
    } else {
        alert("Please enter valid values.");
    }
}







 
 // Function to populate the section dropdown
function populateSectionDropdown() {
    const sectionSelect = document.getElementById('sectionSelect');
    sectionSelect.innerHTML = '<option value="">-- Select an Existing Section --</option>'; // Reset dropdown

    // Get sections from localStorage
    const sections = JSON.parse(localStorage.getItem('sections')) || [];
	
	


        // Create and append default options
        const defaultSections = ['Indoor', 'Outdoor', 'Ground'];
        defaultSections.forEach(section => {
            const option = document.createElement('option');
            option.value = section;
            option.innerText = section;
            sectionSelect.appendChild(option);
        });
	
    sections.forEach(section => {
        const option = document.createElement('option');
        option.value = section;
        option.innerText = section;
        sectionSelect.appendChild(option);
    });
}


 
 
 
	
	
function getAllLocalStorageItems() {
    const length = localStorage.length; // Get the number of items in localStorage
    console.log("Total items in localStorage:", length);
	
	
		
		
	
	let length1=0
    for (let i = 0; i < length; i++) {
        const key = localStorage.key(i); // Get the key at index i
        const value = localStorage.getItem(key); // Get the value associated with the key
		
		
		
				try {
				// Try parsing the value as JSON
				const parsedValue = JSON.parse(value);
				// If it's an array or an object, get the length of its keys
				if (Array.isArray(parsedValue)) {
					length1 = parsedValue.length;
				} else if (typeof parsedValue === 'object' && parsedValue !== null) {
					length1 = Object.keys(parsedValue).length;
				} else {
					// If it's not a JSON object/array, use the string length
					length1 = value.length;
				}
			} catch (e) {
				// If parsing fails, use the string length
				length1 = value.length;
			}
				
				console.log(`Key: ${key}, Value: ${value}, Length: ${length1}`);
				
				try{
				const section = document.querySelector(`.table-grid[data-section="${key}"]`);
				section.innerHTML = '';

				for (let j = 0; j < length1+5; j++){
				
						
				const newTable = document.createElement('div');
					newTable.className = 'table-item';
					newTable.innerText = j+1;
					newTable.onclick = function() { openPopup(this, key); };
					section.appendChild(newTable);
				}
				}catch{
				}
		
	}
}

// Call the function to see the output
getAllLocalStorageItems();




 // Function to stop the sound
        function stopSound() {
            notificationSound.pause(); // Pause the sound
            notificationSound.currentTime = 0; // Reset the sound to the start
        }

        // Listen for confirmation results from the main process
        ipcRenderer.on('stop-sound', () => {
            stopSound(); // Call function to stop the sound
        });


		
		
	 // Request notification permission on page load
        document.addEventListener("DOMContentLoaded", () => {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
        });

        // Function to send reminder notifications
        function sendReminder() {
            if (Notification.permission === "granted") {
                // Display notification
                const notification = new Notification("Reminder: Swiggy Order Update", {
                    body: "Don't miss your order update! Click to view.",
                    icon: "https://path-to-your-image.png"
                });

                // Play sound for attention
                const audio = new Audio("https://example.com/notification-sound.mp3");
                audio.play();

                // Focus on tab if the user clicks the notification
                notification.onclick = () => {
                    window.focus();
                };
            }
        }

        

		 
	
    </script>
</body>
</html>
