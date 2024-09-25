@extends('layouts.admin.master')

@section('title', 'Transfer Details')

@section('content')

<h1>Transfer Price</h1>

<div class="container mx-auto p-4">
    <!-- Form Section -->
    <div class="bg-white p-4 rounded shadow-lg mb-4">
        <form id="transferForm">
            @csrf
            <div class="flex flex-col space-y-4">
                <!-- Country Dropdown -->
                <div class="flex items-center justify-between mb-4">
                    <label for="country" class="text-gray-700 font-semibold">Country</label>
                    <select id="country" name="country_id" class="form-select w-1/2" style="float:right;">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Region Dropdown -->
                <div class="flex items-center justify-between mb-4">
                    <label for="region" class="text-gray-700 font-semibold">Region</label>
                    <select id="region" name="region_id" class="form-select w-1/2" style="float:right;">
                        <option value="">Select Region</option>
                    </select>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <!-- Standard Price Input -->
                    <label for="standard_price" class="text-gray-700 font-semibold">Standard Price</label>
                    <input type="number" id="standard_price" name="standard_price" class="form-input w-1/2" step="0.01" style="float:right;">
                </div>

                <div class="flex items-center justify-between mb-4">
                    <!-- Private Price Input -->
                    <label for="private_price" class="text-gray-700 font-semibold">Private Price</label>
                    <input type="number" id="private_price" name="private_price" class="form-input w-1/2" step="0.01" style="float:right;">
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-primary text-white py-2 px-4 rounded">Save Pricing</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Pricing Data Table -->
    <div id="pricingTable" class="bg-white p-4 rounded shadow-lg">
        <!-- Pricing data will be populated here -->
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const regionSelect = document.getElementById('region');
    const transferForm = document.getElementById('transferForm');
    const pricingTable = document.getElementById('pricingTable');

    countrySelect.addEventListener('change', function () {
        const countryId = this.value;
        fetch(`/api/regions/${countryId}`)
            .then(response => response.json())
            .then(data => {
                regionSelect.innerHTML = '<option value="">Select Region</option>';
                data.forEach(region => {
                    regionSelect.innerHTML += `<option value="${region.id}">${region.name}</option>`;
                });
            });
    });

    transferForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('/admin/transfer/store', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPricingData();
                alert('Pricing saved successfully!');
            } else {
                alert('Error saving pricing.');
            }
        });
    });

    function loadPricingData() {
        fetch('/admin/transfer/fetchPricing')
            .then(response => response.json())
            .then(data => {
                let tableHtml = `
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Country</th>
                                <th class="border border-gray-300 px-4 py-2">Region</th>
                                <th class="border border-gray-300 px-4 py-2">Standard Price</th>
                                <th class="border border-gray-300 px-4 py-2">Private Price</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                data.data.forEach(item => {
                    tableHtml += `
                        <tr>
                            <td class="border border-gray-300 px-4 py-4">${item.country.name}</td>
                            <td class="border border-gray-300 px-4 py-4">${item.region.name}</td>
                            <td class="border border-gray-300 px-4 py-4">£${item.standard_price}</td>
                            <td class="border border-gray-300 px-4 py-4">£${item.private_price}</td>
                        </tr>
                    `;
                });

                tableHtml += `
                    </tbody>
                </table>
                <div class="mt-4">
                    ${data.links}
                </div>
                `;

                pricingTable.innerHTML = tableHtml;
            });
    }

    loadPricingData();
});
</script>
@endsection
