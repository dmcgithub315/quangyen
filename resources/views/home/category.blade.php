@extends('layout.admin')
@section('title', 'Thêm sản phẩm mới')
@section('style')
	<style>

	</style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-semibold mb-4">Danh mục sản phẩm</h5>
                    <button type="button" class="btn btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Thêm mới</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" id="searchCategories" placeholder="Tên danh mục...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Danh mục cha</label>
                                <select class="form-select" id="filterParentCategory">
                                    <option value="">Tất cả danh mục</option>
                                    <option value="null">Danh mục gốc</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="filterStatus">
                                    <option value="">Tất cả</option>
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Tạm dừng</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hiển thị</label>
                                <select class="form-select" id="perPageSelect">
                                    <option value="10">10</option>
                                    <option value="15" selected>15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                                        <i class="fas fa-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Categories List -->
                        <div class="row">
                            <div class="col-12">
                                <div id="categoriesLoading" class="text-center py-4 d-none">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Đang tải danh mục...</p>
                                </div>

                                <div id="categoriesTable">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Hình ảnh</th>
                                                    <th width="20%">Tên danh mục</th>
                                                    <th width="15%">Slug</th>
                                                    <th width="15%">Danh mục cha</th>
                                                    <th width="10%">Thứ tự</th>
                                                    <th width="10%">Trạng thái</th>
                                                    <th width="10%">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody id="categoriesTableBody">
                                                <!-- Categories will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Categories pagination" id="categoriesPagination" class="mt-3">
                                    <!-- Pagination will be loaded here -->
                                </nav>

                                <!-- No Results -->
                                <div id="noResults" class="text-center py-5 d-none">
                                    <div class="mb-3">
                                        <i class="fas fa-search fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">Không tìm thấy danh mục</h5>
                                    <p class="text-muted">Thử thay đổi bộ lọc để tìm kiếm danh mục khác</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categorySlug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categorySlug" name="slug" required>
                                    <div class="form-text">Tự động tạo từ tên danh mục. Có thể chỉnh sửa thủ công.</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoryParent" class="form-label">Danh mục cha</label>
                                    <select class="form-select" id="categoryParent" name="parent_category_id">
                                        <option value="">-- Chọn danh mục cha --</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categorySortOrder" class="form-label">Thứ tự sắp xếp</label>
                                    <input type="number" class="form-control" id="categorySortOrder" name="sort_order" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="categoryImageFile" class="form-label">Hình ảnh</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="categoryImageFile" name="image_file" accept="image/*">
                                        <button class="btn btn-outline-secondary" type="button" id="uploadImageBtn">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Upload
                                        </button>
                                    </div>
                                    <div class="form-text">Chọn ảnh để upload lên cloud (JPEG, PNG, GIF, WebP - Tối đa 5MB)</div>
                                    <div class="invalid-feedback"></div>
                                    
                                    <!-- Hidden input to store the uploaded image URL -->
                                    <input type="hidden" id="categoryImage" name="image">
                                    
                                    <!-- Image preview -->
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                        <button type="button" class="btn btn-sm btn-danger ms-2" id="removeImageBtn">Xóa ảnh</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="categoryActive" name="is_active" checked>
                                <label class="form-check-label" for="categoryActive">
                                    Kích hoạt danh mục
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Thêm danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let isLoading = false;

    // Load categories on page load
    loadCategories();

    // When modal is shown, load data from addnew endpoint
    $('#addCategoryModal').on('show.bs.modal', function (e) {
        loadAddNewData();
    });

    // Auto generate slug from category name
    $('#categoryName').on('input', function() {
        const name = $(this).val();
        const slug = generateSlug(name);
        $('#categorySlug').val(slug);
    });

    // Handle image upload
    $('#uploadImageBtn').on('click', function() {
        const fileInput = $('#categoryImageFile')[0];
        const file = fileInput.files[0];
        
        if (!file) {
            showAlert('error', 'Vui lòng chọn ảnh để upload');
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showAlert('error', 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'Kích thước ảnh không được vượt quá 5MB');
            return;
        }

        uploadImageToCloud(file);
    });

    // Upload image to Cloudinary
    function uploadImageToCloud(file) {
        const uploadBtn = $('#uploadImageBtn');
        const spinner = uploadBtn.find('.spinner-border');
        
        // Show loading state
        uploadBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        const formData = new FormData();
        formData.append('image', file);
        formData.append('folder', 'categories');

        $.ajax({
            url: '/api/images/upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const imageUrl = response.data.url;
                    
                    // Set the hidden input value
                    $('#categoryImage').val(imageUrl);
                    
                    // Show preview
                    $('#previewImg').attr('src', imageUrl);
                    $('#imagePreview').removeClass('d-none');
                    
                    // Clear file input
                    $('#categoryImageFile').val('');
                    
                    showToast('success', '📷 Upload ảnh thành công!');
                } else {
                    showAlert('error', 'Lỗi upload: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Lỗi upload ảnh';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                showAlert('error', errorMessage);
            },
            complete: function() {
                // Hide loading state
                uploadBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    }

    // Remove uploaded image
    $('#removeImageBtn').on('click', function() {
        $('#categoryImage').val('');
        $('#imagePreview').addClass('d-none');
        $('#categoryImageFile').val('');
        showToast('info', '🗑️ Đã xóa ảnh');
    });

    // Function to generate slug from Vietnamese text
    function generateSlug(text) {
        // Vietnamese characters map
        const vietnameseMap = {
            'à': 'a', 'á': 'a', 'ạ': 'a', 'ả': 'a', 'ã': 'a', 'â': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a', 'ẫ': 'a', 'ă': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a',
            'è': 'e', 'é': 'e', 'ẹ': 'e', 'ẻ': 'e', 'ẽ': 'e', 'ê': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
            'ì': 'i', 'í': 'i', 'ị': 'i', 'ỉ': 'i', 'ĩ': 'i',
            'ò': 'o', 'ó': 'o', 'ọ': 'o', 'ỏ': 'o', 'õ': 'o', 'ô': 'o', 'ồ': 'o', 'ố': 'o', 'ộ': 'o', 'ổ': 'o', 'ỗ': 'o', 'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o', 'ở': 'o', 'ỡ': 'o',
            'ù': 'u', 'ú': 'u', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u', 'ư': 'u', 'ừ': 'u', 'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u',
            'ỳ': 'y', 'ý': 'y', 'ỵ': 'y', 'ỷ': 'y', 'ỹ': 'y',
            'đ': 'd',
            'À': 'A', 'Á': 'A', 'Ạ': 'A', 'Ả': 'A', 'Ã': 'A', 'Â': 'A', 'Ầ': 'A', 'Ấ': 'A', 'Ậ': 'A', 'Ẩ': 'A', 'Ẫ': 'A', 'Ă': 'A', 'Ằ': 'A', 'Ắ': 'A', 'Ặ': 'A', 'Ẳ': 'A', 'Ẵ': 'A',
            'È': 'E', 'É': 'E', 'Ẹ': 'E', 'Ẻ': 'E', 'Ẽ': 'E', 'Ê': 'E', 'Ề': 'E', 'Ế': 'E', 'Ệ': 'E', 'Ể': 'E', 'Ễ': 'E',
            'Ì': 'I', 'Í': 'I', 'Ị': 'I', 'Ỉ': 'I', 'Ĩ': 'I',
            'Ò': 'O', 'Ó': 'O', 'Ọ': 'O', 'Ỏ': 'O', 'Õ': 'O', 'Ô': 'O', 'Ồ': 'O', 'Ố': 'O', 'Ộ': 'O', 'Ổ': 'O', 'Ỗ': 'O', 'Ơ': 'O', 'Ờ': 'O', 'Ớ': 'O', 'Ợ': 'O', 'Ở': 'O', 'Ỡ': 'O',
            'Ù': 'U', 'Ú': 'U', 'Ụ': 'U', 'Ủ': 'U', 'Ũ': 'U', 'Ư': 'U', 'Ừ': 'U', 'Ứ': 'U', 'Ự': 'U', 'Ử': 'U', 'Ữ': 'U',
            'Ỳ': 'Y', 'Ý': 'Y', 'Ỵ': 'Y', 'Ỷ': 'Y', 'Ỹ': 'Y',
            'Đ': 'D'
        };

        return text
            .split('')
            .map(char => vietnameseMap[char] || char)
            .join('')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-+/g, '-');
    }

    // Load data for adding new category
    function loadAddNewData() {
        $.ajax({
            url: '/api/categories/addnew',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate parent categories for modal
                    const parentSelect = $('#categoryParent');
                    parentSelect.empty().append('<option value="">-- Chọn danh mục cha --</option>');
                    
                    response.data.parent_categories.forEach(function(category) {
                        parentSelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });

                    // Also populate filter dropdown
                    const filterSelect = $('#filterParentCategory');
                    filterSelect.find('option:gt(1)').remove(); // Keep "Tất cả" and "Danh mục gốc"
                    
                    response.data.parent_categories.forEach(function(category) {
                        filterSelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });

                    // Set default values
                    if (response.data.default_values) {
                        $('#categorySortOrder').val(response.data.default_values.sort_order);
                        $('#categoryActive').prop('checked', response.data.default_values.is_active);
                    }
                } else {
                    showAlert('error', 'Không thể tải dữ liệu: ' + response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Lỗi kết nối server');
                console.error(xhr);
            }
        });
    }

    // Handle form submission
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const spinner = submitBtn.find('.spinner-border');
        
        // Show loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        // Clear previous validation errors
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Prepare form data
        const formData = {
            name: $('#categoryName').val(),
            slug: $('#categorySlug').val(),
            description: $('#categoryDescription').val(),
            parent_category_id: $('#categoryParent').val() || null,
            image: $('#categoryImage').val(),
            is_active: $('#categoryActive').is(':checked'),
            sort_order: $('#categorySortOrder').val() || 0
        };

        // Submit to store endpoint
        $.ajax({
            url: '/api/categories',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', '✅ Thêm danh mục thành công!');
                    $('#addCategoryModal').modal('hide');
                    $('#addCategoryForm')[0].reset();
                    // Reload categories list
                    loadCategories();
                } else {
                    showAlert('error', 'Lỗi: ' + response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    }
                } else {
                    showAlert('error', 'Lỗi server: ' + (xhr.responseJSON?.message || 'Không xác định'));
                }
            },
            complete: function() {
                // Hide loading state
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Reset form when modal is hidden
    $('#addCategoryModal').on('hidden.bs.modal', function (e) {
        $('#addCategoryForm')[0].reset();
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    // Helper function to show toasts
    function showToast(type, message) {
        const config = {
            text: message,
            duration: 4000,
            close: true,
            gravity: "top",
            position: "right",
            stopOnFocus: true,
        };

        switch(type) {
            case 'success':
                config.backgroundColor = "linear-gradient(to right, #00b09b, #96c93d)";
                break;
            case 'error':
                config.backgroundColor = "linear-gradient(to right, #ff5f6d, #ffc371)";
                break;
            case 'info':
                config.backgroundColor = "linear-gradient(to right, #667eea, #764ba2)";
                break;
            case 'warning':
                config.backgroundColor = "linear-gradient(to right, #f093fb, #f5576c)";
                break;
            default:
                config.backgroundColor = "linear-gradient(to right, #11998e, #38ef7d)";
        }

        Toastify(config).showToast();
    }

    // Helper function to show alerts (for non-success messages)
    function showAlert(type, message) {
        if (type === 'success') {
            showToast('success', message);
            return;
        }

        const alertClass = type === 'info' ? 'alert-info' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Insert alert at the top of the page
        $('.container-fluid').prepend(alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Load categories with filters
    function loadCategories(page = 1) {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        
        // Show loading
        $('#categoriesLoading').removeClass('d-none');
        $('#categoriesTable').addClass('d-none');
        $('#noResults').addClass('d-none');
        $('#categoriesPagination').empty();

        // Prepare filters
        const filters = {
            page: page,
            per_page: $('#perPageSelect').val(),
            search: $('#searchCategories').val().trim(),
            parent_id: $('#filterParentCategory').val(),
            active: $('#filterStatus').val()
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (filters[key] === '' || filters[key] === null) {
                delete filters[key];
            }
        });

        $.ajax({
            url: '/api/categories',
            method: 'GET',
            data: filters,
            success: function(response) {
                if (response.success) {
                    displayCategories(response.data);
                    if (response.data.data.length === 0 && page === 1) {
                        showNoResults();
                    }
                } else {
                    showAlert('error', 'Lỗi tải danh mục: ' + response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Lỗi kết nối: ' + (xhr.responseJSON?.message || 'Không thể tải danh mục'));
                console.error(xhr);
            },
            complete: function() {
                isLoading = false;
                $('#categoriesLoading').addClass('d-none');
            }
        });
    }

    // Display categories in table
    function displayCategories(data) {
        const tbody = $('#categoriesTableBody');
        tbody.empty();

        if (data.data.length === 0) {
            showNoResults();
            return;
        }

        $('#categoriesTable').removeClass('d-none');

        data.data.forEach(function(category, index) {
            const row = `
                <tr>
                    <td>${(data.current_page - 1) * data.per_page + index + 1}</td>
                    <td>
                        ${category.image ? 
                            `<img src="${category.image}" alt="${category.name}" class="img-thumbnail" style="width: 60px; height: 45px; object-fit: cover;">` : 
                            '<span class="text-muted">Chưa có ảnh</span>'
                        }
                    </td>
                    <td>
                        <strong>${category.name}</strong>
                        ${category.description ? `<br><small class="text-muted">${category.description.substring(0, 50)}${category.description.length > 50 ? '...' : ''}</small>` : ''}
                    </td>
                    <td><code>${category.slug}</code></td>
                    <td>${category.parent ? category.parent.name : '<span class="text-muted">Danh mục gốc</span>'}</td>
                    <td><span class="badge bg-secondary">${category.sort_order}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm ${category.is_active ? 'btn-success' : 'btn-secondary'}" 
                                onclick="toggleCategoryStatus(${category.id})" title="Click để thay đổi trạng thái">
                            ${category.is_active ? 'Hoạt động' : 'Tạm dừng'}
                        </button>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="editCategory(${category.id})" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteCategory(${category.id})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Generate pagination
        generatePagination(data);
    }

    // Show no results
    function showNoResults() {
        $('#categoriesTable').addClass('d-none');
        $('#noResults').removeClass('d-none');
        $('#categoriesPagination').empty();
    }

    // Generate pagination
    function generatePagination(data) {
        const pagination = $('#categoriesPagination');
        pagination.empty();

        if (data.last_page <= 1) return;

        let paginationHtml = '<ul class="pagination justify-content-center">';
        
        // Previous button
        paginationHtml += `
            <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="${data.current_page > 1 ? `loadCategories(${data.current_page - 1})` : 'return false'}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;

        // Page numbers
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadCategories(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="${data.current_page < data.last_page ? `loadCategories(${data.current_page + 1})` : 'return false'}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;

        paginationHtml += '</ul>';
        
        // Add pagination info
        paginationHtml += `
            <div class="text-center mt-2">
                <small class="text-muted">
                    Hiển thị ${data.from} - ${data.to} của ${data.total} danh mục
                </small>
            </div>
        `;

        pagination.html(paginationHtml);
    }

    // Filter event handlers
    $('#searchCategories').on('keyup', debounce(function() {
        loadCategories(1);
    }, 500));

    $('#filterParentCategory, #filterStatus, #perPageSelect').on('change', function() {
        loadCategories(1);
    });

    $('#resetFilters').on('click', function() {
        $('#searchCategories').val('');
        $('#filterParentCategory').val('');
        $('#filterStatus').val('');
        $('#perPageSelect').val('15');
        loadCategories(1);
    });

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Toggle category status
    window.toggleCategoryStatus = function(id) {
        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái danh mục này?')) {
            $.ajax({
                url: `/api/categories/${id}/toggle-status`,
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '🔄 Cập nhật trạng thái thành công!');
                        loadCategories(currentPage);
                    } else {
                        showAlert('error', 'Lỗi: ' + response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Lỗi cập nhật trạng thái: ' + (xhr.responseJSON?.message || 'Không xác định'));
                }
            });
        }
    };

    // Edit category (placeholder)
    window.editCategory = function(id) {
        showToast('info', '⚙️ Chức năng chỉnh sửa sẽ được phát triển trong phiên bản tiếp theo');
    };

    // Delete category
    window.deleteCategory = function(id) {
        if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Hành động này không thể hoàn tác!')) {
            $.ajax({
                url: `/api/categories/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '🗑️ Xóa danh mục thành công!');
                        loadCategories(currentPage);
                    } else {
                        showAlert('error', 'Lỗi: ' + response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Lỗi xóa danh mục: ' + (xhr.responseJSON?.message || 'Không xác định'));
                }
            });
        }
    };
});
</script>
@endsection
