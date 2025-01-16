
{{-- <form action="http://127.0.0.1:8000/api/provider/information" method="POST">
    @csrf
    <div class="form-group">
        <label for="businessName">Business Name</label>
        <input type="text" name="providerData[businessName]" class="form-control" id="businessName" value="Test Business Ltd." required>
    </div>

    <div class="form-group">
        <label for="phoneNumber">Phone Number</label>
        <input type="text" name="providerData[phoneNumber]" class="form-control" id="phoneNumber" value="+1234567890" required>
    </div>

    <div class="form-group">
        <label for="companyEmail">Company Email</label>
        <input type="email" name="providerData[companyEmail]" class="form-control" id="companyEmail" value="test@business.com" required>
    </div>

    <div class="form-group">
        <label for="providerNameEn">Provider Name (English)</label>
        <input type="text" name="providerData[providerNameEn]" class="form-control" id="providerNameEn" value="Test Provider">
    </div>

    <div class="form-group">
        <label for="providerNameAr">Provider Name (Arabic)</label>
        <input type="text" name="providerData[providerNameAr]" class="form-control" id="providerNameAr" value="مزود الاختبار">
    </div>

    <div class="form-group">
        <label for="crNumber">CR Number</label>
        <input type="text" name="providerData[crNumber]" class="form-control" id="crNumber" value="CR12345678">
    </div>

    <div class="form-group">
        <label for="instagramUrl">Instagram URL</label>
        <input type="url" name="providerData[instagramUrl]" class="form-control" id="instagramUrl" value="https://instagram.com/testprovider">
    </div>

    <div class="form-group">
        <label for="website">Website</label>
        <input type="url" name="providerData[website]" class="form-control" id="website" value="https://testbusiness.com">
    </div>

    <div class="form-group">
        <label for="startDate">Start Date</label>
        <input type="date" name="providerData[startDate]" class="form-control" id="startDate" value="2024-01-01">
    </div>

    <div class="form-group">
        <label for="endDate">End Date</label>
        <input type="date" name="providerData[endDate]" class="form-control" id="endDate" value="2024-12-31">
    </div>

    <div class="form-group">
        <label for="address[office]">Office</label>
        <input type="text" name="providerData[address][office]" class="form-control" id="office" value="Office 101, Test Building">
    </div>

    <div class="form-group">
        <label for="address[road]">Road</label>
        <input type="text" name="providerData[address][road]" class="form-control" id="road" value="Test Road">
    </div>

    <div class="form-group">
        <label for="address[block]">Block</label>
        <input type="text" name="providerData[address][block]" class="form-control" id="block" value="Block A">
    </div>

    <div class="form-group">
        <label for="address[city]">City</label>
        <input type="text" name="providerData[address][city]" class="form-control" id="city" value="Test City">
    </div>

    <div class="form-group">
        <label for="availabilityDays">Availability Days</label>
        <input type="text" name="providerData[availabilityDays][]" class="form-control" placeholder="Enter availability days, separated by commas" value="Monday, Wednesday, Friday">
    </div>

    <div class="form-group">
        <label for="availabilityHours[start]">Availability Start Time</label>
        <input type="time" name="providerData[availabilityHours][start]" class="form-control" id="startTime" value="09:00">
    </div>

    <div class="form-group">
        <label for="availabilityHours[end]">Availability End Time</label>
        <input type="time" name="providerData[availabilityHours][end]" class="form-control" id="endTime" value="17:00">
    </div>

    <div class="form-group">
        <label for="authorizedPerson">Authorized Person</label>
        <input type="text" name="providerData[authorizedPerson][]" class="form-control" placeholder="Enter authorized person" value="John Doe">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Provider Information</title>
</head>
<body>
    <h1>Provider Information Form</h1>
    <form action="http://127.0.0.1:8000/api/provider/information" method="POST" enctype="multipart/form-data">
        @csrf <!-- Include CSRF token for Laravel -->
        <div>
            <label for="providerType">Provider Type:</label>
            <input type="text" id="providerType" name="providerData[providerType]" required>
        </div>
        <div>
            <label for="businessName">Business Name:</label>
            <input type="text" id="businessName" name="providerData[businessName]" required>
        </div>
        <div>
            <label for="phoneNumber">Phone Number:</label>
            <input type="tel" id="phoneNumber" name="providerData[phoneNumber]" required>
        </div>
        <div>
            <label for="companyEmail">Company Email:</label>
            <input type="email" id="companyEmail" name="providerData[companyEmail]" required>
        </div>
        <div>
            <label for="providerNameEn">Provider Name (English):</label>
            <input type="text" id="providerNameEn" name="providerData[providerNameEn]" required>
        </div>
        <div>
            <label for="providerNameAr">Provider Name (Arabic):</label>
            <input type="text" id="providerNameAr" name="providerData[providerNameAr]" required>
        </div>
        <div>
            <label for="crNumber">CR Number:</label>
            <input type="text" id="crNumber" name="providerData[crNumber]" required>
        </div>
        <div>
            <label for="instagramUrl">Instagram URL:</label>
            <input type="url" id="instagramUrl" name="providerData[instagramUrl]">
        </div>
        <div>
            <label for="website">Website:</label>
            <input type="url" id="website" name="providerData[website]">
        </div>
        <div>
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="providerData[startDate]">
        </div>
        <div>
            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="providerData[endDate]">
        </div>
        <fieldset>
            <legend>Address</legend>
            <div>
                <label for="office">Office:</label>
                <input type="text" id="office" name="providerData[address][office]">
            </div>
            <div>
                <label for="road">Road:</label>
                <input type="text" id="road" name="providerData[address][road]">
            </div>
            <div>
                <label for="block">Block:</label>
                <input type="text" id="block" name="providerData[address][block]">
            </div>
            <div>
                <label for="city">City:</label>
                <input type="text" id="city" name="providerData[address][city]">
            </div>
        </fieldset>
        <div>
            <label for="profileImage">Profile Image:</label>
            <input type="file" id="profileImage" name="providerData[profile_image]" accept="image/*">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>


{{-- <form action="http://127.0.0.1:8000/api/provider/information" method="POST">
    @csrf

    <!-- Provider Type -->
    <label for="providerType">Provider Type:</label>
    <select name="providerType" id="providerType">
        <option value="doctor">Doctor</option>
        <option value="pet shop">Pet Shop</option>
        <option value="groomer">Groomer</option>
        <option value="pet clinic">Pet Clinic</option>
        <option value="trainer">Trainer</option>
    </select>

    <!-- Business Name -->
    <label for="businessName">Business Name:</label>
    <input type="text" name="businessName" id="businessName" value="Happy Paws Clinic">

    <!-- Phone Number -->
    <label for="phoneNumber">Phone Number:</label>
    <input type="text" name="phoneNumber" id="phoneNumber" value="1234567890">

    <!-- Company Email -->
    <label for="companyEmail">Company Email:</label>
    <input type="email" name="companyEmail" id="companyEmail" value="info@happypaws.com">

    <!-- Provider Name (English) -->
    <label for="providerNameEn">Provider Name (English):</label>
    <input type="text" name="providerNameEn" id="providerNameEn" value="Happy Paws">

    <!-- Provider Name (Arabic) -->
    <label for="providerNameAr">Provider Name (Arabic):</label>
    <input type="text" name="providerNameAr" id="providerNameAr" value="سعادة الكفوف">

    <!-- CR Number -->
    <label for="crNumber">CR Number:</label>
    <input type="text" name="crNumber" id="crNumber" value="CR-12345">

    <!-- Instagram URL -->
    <label for="instagramUrl">Instagram URL:</label>
    <input type="text" name="instagramUrl" id="instagramUrl" value="https://instagram.com/happypawsclinic">

    <!-- Office -->
    <label for="office">Office:</label>
    <input type="text" name="office" id="office" value="Office 23, Building 12">

    <!-- Road -->
    <label for="road">Road:</label>
    <input type="text" name="road" id="road" value="Road 45">

    <!-- Block -->
    <label for="block">Block:</label>
    <input type="text" name="block" id="block" value="Block 7">

    <!-- City -->
    <label for="city">City:</label>
    <input type="text" name="city" id="city" value="Manama">

    <!-- Location -->
    <label for="location">Location (Optional):</label>
    <input type="text" name="location" id="location" placeholder="Full Address">

    <!-- Availability Days -->
    <label for="availabilityDays">Availability Days (JSON Format):</label>
    <textarea name="availabilityDays" id="availabilityDays">["Monday", "Wednesday", "Friday"]</textarea>

    <!-- Availability Hours -->
    <label for="availabilityStart">Availability Start Time:</label>
    <input type="text" name="availabilityStart" id="availabilityStart" value="09:00 AM">

    <label for="availabilityEnd">Availability End Time:</label>
    <input type="text" name="availabilityEnd" id="availabilityEnd" value="05:00 PM">

    <!-- Authorized Person -->
    <h4>Authorized Person</h4>
    <label for="authorizedPersonName">Name:</label>
    <input type="text" name="authorizedPerson[name]" id="authorizedPersonName" value="John Doe">

    <label for="authorizedPersonPosition">Position:</label>
    <input type="text" name="authorizedPerson[position]" id="authorizedPersonPosition" value="Manager">

    <label for="authorizedPersonContactNumber">Contact Number:</label>
    <input type="text" name="authorizedPerson[contactNumber]" id="authorizedPersonContactNumber" value="9876543210">

    <label for="authorizedPersonEmail">Email:</label>
    <input type="email" name="authorizedPerson[email]" id="authorizedPersonEmail" value="johndoe@example.com">

    <!-- Submit Button -->
    <button type="submit">Submit</button>
</form> --}}
{{-- <br>
<br>
<br>
<br>
<br>
<br>

<div class="container">
    <h2>Create New Service</h2>
    <form action="{{ route('provider_all.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Title (English)</label>
            <input type="text" name="title" id="title" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="title_ar">Title (Arabic)</label>
            <input type="text" name="title_ar" id="title_ar" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="short_description">Short Description (English)</label>
            <textarea name="short_description" id="short_description" class="form-control"   ></textarea>
        </div>

        <div class="form-group">
            <label for="short_description_ar">Short Description (Arabic)</label>
            <textarea name="short_description_ar" id="short_description_ar" class="form-control"   ></textarea>
        </div>

        <div class="form-group">
            <label for="old_price">Old Price</label>
            <input type="number" step="0.01" name="old_price" id="old_price" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="new_price">New Price</label>
            <input type="number" step="0.01" name="new_price" id="new_price" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="percentage">Percentage</label>
            <input type="number" step="0.01" name="percentage" id="percentage" class="form-control">
        </div>

        <div class="form-group">
            <label for="discount">Discount</label>
            <input type="number" step="0.01" name="discount" id="discount" class="form-control">
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="pet_type">Pet Type</label>
            <input type="text" name="pet_type" id="pet_type" class="form-control">
        </div>

        <div class="form-group">
            <label for="provider_id">Provider ID</label>
            <input type="number" name="provider_id" id="provider_id" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="service_name_eng">Service Name (English)</label>
            <input type="text" name="service_name_eng" id="service_name_eng" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="service_name_ara">Service Name (Arabic)</label>
            <input type="text" name="service_name_ara" id="service_name_ara" class="form-control"   >
        </div>

        <div class="form-group">
            <label for="service_description_en">Service Description (English)</label>
            <textarea name="service_description_en" id="service_description_en" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="service_description_ar">Service Description (Arabic)</label>
            <textarea name="service_description_ar" id="service_description_ar" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Service</button>
    </form>
</div> --}}

{{-- 

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-5">
    <h2>Add Category</h2>
    <form id="addCategoryForm" method="POST" action="{{route('provider_all.addCategory')}}">
        @csrf <!-- Include CSRF token for security -->
        <div class="row">
            <!-- Name -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter category name" required>
            </div>
            
            <!-- Total Stock -->
            <div class="col-md-6 mb-3">
                <label for="totalStock" class="form-label">Total Stock</label>
                <input type="number" id="totalStock" name="totalStock" class="form-control" placeholder="Enter total stock" required>
            </div>
        </div>

        <div class="row">
            <!-- Description -->
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="Enter category description" rows="4" required></textarea>
            </div>
        </div>

        <div class="row">
            <!-- Image URL -->
            <div class="col-md-6 mb-3">
                <label for="imageUrl" class="form-label">Image URL</label>
                <input type="url" id="imageUrl" name="imageUrl" class="form-control" placeholder="Enter image URL" required>
            </div>

            <!-- Price -->
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" placeholder="Enter price (optional)">
            </div>
        </div>

        <div class="row">
            <!-- Availability -->
            <div class="col-md-6 mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select id="availability" name="availability" class="form-control">
                    <option value="1">Available</option>
                    <option value="0">Not Available</option>
                </select>
            </div>

            <!-- Total Sold -->
            <div class="col-md-6 mb-3">
                <label for="totalSold" class="form-label">Total Sold</label>
                <input type="number" id="totalSold" name="totalSold" class="form-control" placeholder="Enter total sold (optional)">
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
</div>
{{-- 
<!-- AJAX Script -->
<script>
    document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Gather form data
        const formData = {
            tableData: [{
                name: document.getElementById('name').value,
                totalStock: document.getElementById('totalStock').value,
                description: document.getElementById('description').value,
                imageUrl: document.getElementById('imageUrl').value,
                price: document.getElementById('price').value || 0, // Default to 0 if empty
                availability: document.getElementById('availability').value,
                totalSold: document.getElementById('totalSold').value || 0, // Default to 0 if empty
            }]
        };

        // Send the data via AJAX
        fetch("{{ route('provider_all.addCategory') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message); // Show success message
                document.getElementById('addCategoryForm').reset(); // Reset the form
            } else if (data.error) {
                alert(data.error); // Show error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the category.');
        });
    });
</script>
 --}} --}}
