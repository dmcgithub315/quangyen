@extends('layout.admin')
@section('title', 'Thêm sản phẩm mới')
@section('style')
	<style>
        /* CKEditor custom styles */
        .ck-editor__editable_inline {
            min-height: 200px;
        }
        
        .ck.ck-editor {
            max-width: 100%;
        }
        
        .ck-content {
            font-family: inherit;
        }
	</style>
    
    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-semibold mb-4">Sản phẩm</h5>
                    <button type="button" class="btn btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#addProductModal">Thêm mới</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" id="searchProducts" placeholder="Tên sản phẩm...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" id="filterCategory">
                                    <option value="">Tất cả danh mục</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái kho</label>
                                <select class="form-select" id="filterStockStatus">
                                    <option value="">Tất cả</option>
                                    <option value="in_stock">Còn hàng</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                    <option value="on_backorder">Đặt trước</option>
                                </select>
                            </div>
                            <!-- <div class="col-md-2">
                                <label class="form-label">Hiển thị</label>
                                <select class="form-select" id="perPageSelect">
                                    <option value="10">10</option>
                                    <option value="15" selected>15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div> -->
                            <div class="col-md-2">
                                <label class="form-label">Sắp xếp</label>
                                <select class="form-select" id="sortBy">
                                    <option value="created_at">Mới nhất</option>
                                    <option value="name">Tên A-Z</option>
                                    <option value="price">Giá thấp</option>
                                    <option value="rating">Đánh giá</option>
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

                        <!-- Products List -->
                        <div class="row">
                            <div class="col-12">
                                <div id="productsLoading" class="text-center py-4 d-none">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Đang tải sản phẩm...</p>
                                </div>

                                <div id="productsTable">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="20%">Tên sản phẩm</th>
                                                    <th width="20%">Hãng</th>
                                                    <th width="10%">Giá</th>
                                                    <th width="10%">Đơn vị tính</th>
                                                    <th width="8%">Kho</th>
                                                    <th width="20%">Trạng thái kho</th>
                                                    <th width="17%">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productList">
                                                <!-- Product list will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Products pagination" id="productsPagination" class="mt-3">
                                    <!-- Pagination will be loaded here -->
                                </nav>

                                <!-- No Results -->
                                <div id="noResults" class="text-center py-5 d-none">
                                    <div class="mb-3">
                                        <i class="fas fa-search fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">Không tìm thấy sản phẩm</h5>
                                    <p class="text-muted">Thử thay đổi bộ lọc để tìm kiếm sản phẩm khác</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <input type="hidden" name="productId" id="productId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="productName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productOrigin" class="form-label">Hãng</label>
                                    <input type="text" class="form-control" id="productOrigin" name="origin">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Giá</label>
                                    <input type="text" class="form-control" id="productPrice" name="price" placeholder="VD: 100.000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productPriceUnit" class="form-label">Đơn vị tính</label>
                                    <input type="text" class="form-control" id="productPriceUnit" name="price_unit">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStockQuantity" class="form-label">Số lượng</label>
                                    <input type="number" class="form-control" id="productStockQuantity" name="stock_quantity">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStockStatus" class="form-label">Trạng thái kho</label>
                                    <select class="form-select" id="productStockStatus" name="stock_status">
                                        <option value="in_stock">Còn hàng</option>
                                        <option value="out_of_stock">Hết hàng</option>
                                        <option value="on_backorder">Đặt trước</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="productDescription" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="addProductForm" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Chỉnh sửa sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductName" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editProductName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductOrigin" class="form-label">Hãng</label>
                                    <input type="text" class="form-control" id="editProductOrigin" name="origin">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductPrice" class="form-label">Giá</label>
                                    <input type="text" class="form-control" id="editProductPrice" name="price" placeholder="VD: 100.000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductPriceUnit" class="form-label">Đơn vị tính</label>
                                    <input type="text" class="form-control" id="editProductPriceUnit" name="price_unit">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductStockQuantity" class="form-label">Số lượng</label>
                                    <input type="number" class="form-control" id="editProductStockQuantity" name="stock_quantity">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductStockStatus" class="form-label">Trạng thái kho</label>
                                    <select class="form-select" id="editProductStockStatus" name="stock_status">
                                        <option value="in_stock">Còn hàng</option>
                                        <option value="out_of_stock">Hết hàng</option>
                                        <option value="on_backorder">Đặt trước</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="editProductDescription" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="editProductForm" class="btn btn-primary">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    let currentPage = 1;
    let perPage = 15;
    let totalPages = 1;
    let descriptionEditor = null;
    let editDescriptionEditor = null;

    // Initialize page
    $(document).ready(function() {
        loadProducts();
        setupEventListeners();
        initializeCKEditor();
    });

    // Initialize CKEditor
    function initializeCKEditor() {
        // Initialize for add form
        ClassicEditor
            .create(document.querySelector('#productDescription'))
            .then(editor => {
                descriptionEditor = editor;
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                showToast('Lỗi khởi tạo trình soạn thảo', 'error');
            });

        // Initialize for edit form
        ClassicEditor
            .create(document.querySelector('#editProductDescription'))
            .then(editor => {
                editDescriptionEditor = editor;
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                showToast('Lỗi khởi tạo trình soạn thảo', 'error');
            });
    }

    // Load products
    function loadProducts() {
        $('#productsLoading').removeClass('d-none');
        $('#productsTable').addClass('d-none');
        $('#noResults').addClass('d-none');

        // Get filter values
        const search = $('#searchProducts').val();
        const stockStatus = $('#filterStockStatus').val();
        const sortBy = $('#sortBy').val();

        // Only include filters if they have values
        const params = {
            page: currentPage,
            per_page: perPage
        };

        if (search) params.search = search;
        if (stockStatus) params.stock_status = stockStatus;
        if (sortBy) params.sort_by = sortBy;

        $.ajax({
            url: '/api/products',
            method: 'GET',
            data: params,
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    displayProducts(response.data);
                    $('#productsLoading').addClass('d-none');
                    $('#productsTable').removeClass('d-none');
                } else {
                    $('#productsLoading').addClass('d-none');
                    $('#noResults').removeClass('d-none');
                }
            },
            error: function(xhr) {
                console.error('Error loading products:', xhr);
                showToast('Không thể tải danh sách sản phẩm', 'error');
                $('#productsLoading').addClass('d-none');
                $('#noResults').removeClass('d-none');
            }
        });
    }

    // Display products in table
    function displayProducts(products) {
        const $tbody = $('#productList');
        $tbody.empty();

        products.forEach((product, index) => {
            const row = `
                <tr>
                    <td>${(currentPage - 1) * perPage + index + 1}</td>
                    <td>${product.name}</td>
                    <td>${product.origin || '-'}</td>
                    <td>${product.price ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price) : 'Liên hệ'}</td>
                    <td>${product.price_unit || '-'}</td>
                    <td>${product.stock_quantity || 0}</td>
                    <td>
                        <span class="badge ${getStockStatusBadgeClass(product.stock_status)}">
                            ${getStockStatusText(product.stock_status)}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" onclick="editProduct(${product.id})" title="Chỉnh sửa">
                                <i class="fas fa-edit me-1"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})" title="Xóa">
                                <i class="fas fa-trash me-1"></i> Xóa
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    // Edit product
    function editProduct(id) {
        // Reset form first
        $('#editProductForm')[0].reset();
        $('#editProductId').val('');
        if (editDescriptionEditor) {
            editDescriptionEditor.setData('');
        }
        
        // Show modal
        const modalElement = document.getElementById('editProductModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        // Fetch product data
        $.ajax({
            url: `/api/products/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.data) {
                    const product = response.data;
                    // Fill form with product data
                    $('#editProductId').val(product.id);
                    $('#editProductName').val(product.name);
                    $('#editProductOrigin').val(product.origin || '');
                    $('#editProductPrice').val(formatPriceForEdit(product.price));
                    $('#editProductPriceUnit').val(product.price_unit || '');
                    $('#editProductStockQuantity').val(product.stock_quantity || '');
                    $('#editProductStockStatus').val(product.stock_status);
                    if (editDescriptionEditor) {
                        editDescriptionEditor.setData(product.description || '');
                    }
                } else {
                    showToast('Không tìm thấy thông tin sản phẩm', 'error');
                    modal.hide();
                }
            },
            error: function(xhr) {
                console.error('Error loading product:', xhr);
                showToast('Không thể tải thông tin sản phẩm', 'error');
                modal.hide();
            }
        });
    }

    // Delete product
    function deleteProduct(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            $.ajax({
                url: `/api/products/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showToast('Sản phẩm đã được xóa thành công', 'success');
                    loadProducts();
                },
                error: function(xhr) {
                    console.error('Error deleting product:', xhr);
                    showToast('Không thể xóa sản phẩm', 'error');
                }
            });
        }
    }

    // Helper functions
    function getStockStatusBadgeClass(status) {
        switch(status) {
            case 'in_stock': return 'bg-success';
            case 'out_of_stock': return 'bg-danger';
            case 'on_backorder': return 'bg-warning';
            default: return 'bg-secondary';
        }
    }

    function getStockStatusText(status) {
        switch(status) {
            case 'in_stock': return 'Còn hàng';
            case 'out_of_stock': return 'Hết hàng';
            case 'on_backorder': return 'Đặt trước';
            default: return 'Không xác định';
        }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Price formatting for add product form
        $('#productPrice').on('input', function() {
            formatPriceInput(this);
        });

        // Price formatting for edit product form
        $('#editProductPrice').on('input', function() {
            formatPriceInput(this);
        });

        // Add product form submission
        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Remove formatting from price before sending
            const priceValue = $('#productPrice').val().replace(/\./g, '');
            formData.set('price', priceValue);
            
            // Add description from CKEditor
            if (descriptionEditor) {
                formData.set('description', descriptionEditor.getData());
            }
            
            // Show loading state
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            
            // Prepare request data
            const requestData = {};
            formData.forEach((value, key) => {
                requestData[key] = value;
            });
            
            // Send request
            $.ajax({
                url: '/api/products',
                method: 'POST',
                data: requestData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    modal.hide();
                    
                    // Show success message
                    showToast('Sản phẩm đã được thêm mới thành công', 'success');
                    
                    // Reload products
                    loadProducts();
                    
                    // Reset form
                    $('#addProductForm')[0].reset();
                    if (descriptionEditor) {
                        descriptionEditor.setData('');
                    }
                },
                error: function(xhr) {
                    console.error('Error saving product:', xhr);
                    showToast('Có lỗi xảy ra khi lưu sản phẩm', 'error');
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Edit product form submission
        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = formData.get('id');
            
            // Remove formatting from price before sending
            const priceValue = $('#editProductPrice').val().replace(/\./g, '');
            formData.set('price', priceValue);
            
            // Add description from CKEditor
            if (editDescriptionEditor) {
                formData.set('description', editDescriptionEditor.getData());
            }
            
            // Show loading state
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            
            // Prepare request data
            const requestData = {};
            formData.forEach((value, key) => {
                requestData[key] = value;
            });
            
            // Send request
            $.ajax({
                url: `/api/products/${productId}`,
                method: 'PUT',
                data: requestData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    modal.hide();
                    
                    // Show success message
                    showToast('Sản phẩm đã được cập nhật thành công', 'success');
                    
                    // Reload products
                    loadProducts();
                    
                    // Reset form
                    $('#editProductForm')[0].reset();
                    if (editDescriptionEditor) {
                        editDescriptionEditor.setData('');
                    }
                },
                error: function(xhr) {
                    console.error('Error updating product:', xhr);
                    showToast('Có lỗi xảy ra khi cập nhật sản phẩm', 'error');
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#searchProducts').val('');
            $('#filterStockStatus').val('');
            $('#sortBy').val('created_at');
            currentPage = 1;
            loadProducts();
        });

        // Filter changes
        $('#searchProducts, #filterStockStatus, #sortBy').on('change', function() {
            currentPage = 1;
            loadProducts();
        });

        // Modal reset on hide
        $('#addProductModal').on('hidden.bs.modal', function() {
            $('#addProductForm')[0].reset();
            if (descriptionEditor) {
                descriptionEditor.setData('');
            }
        });

        $('#editProductModal').on('hidden.bs.modal', function() {
            $('#editProductForm')[0].reset();
            if (editDescriptionEditor) {
                editDescriptionEditor.setData('');
            }
        });
    }

    // Format price input with dots as thousand separators
    function formatPriceInput(input) {
        // Get the current value and remove all non-digits
        let value = input.value.replace(/[^\d]/g, '');
        
        // If empty, just return
        if (value === '') {
            input.value = '';
            return;
        }
        
        // Add dots every 3 digits from right to left
        let formattedValue = '';
        let counter = 0;
        
        for (let i = value.length - 1; i >= 0; i--) {
            if (counter > 0 && counter % 3 === 0) {
                formattedValue = '.' + formattedValue;
            }
            formattedValue = value[i] + formattedValue;
            counter++;
        }
        
        input.value = formattedValue;
    }

    // Format price for display in edit form
    function formatPriceForEdit(price) {
        if (!price || price === '0' || price === 0) return '';
        
        // Convert to string and remove decimals if it's a whole number
        let priceStr = parseFloat(price).toString();
        
        // Remove all non-digits
        let value = priceStr.replace(/[^\d]/g, '');
        
        if (value === '') return '';
        
        // Add dots every 3 digits from right to left
        let formattedValue = '';
        let counter = 0;
        
        for (let i = value.length - 1; i >= 0; i--) {
            if (counter > 0 && counter % 3 === 0) {
                formattedValue = '.' + formattedValue;
            }
            formattedValue = value[i] + formattedValue;
            counter++;
        }
        
        return formattedValue;
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        const $toastContainer = $('#toastContainer');
        if ($toastContainer.length === 0) {
            $('body').append('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        }
        
        $('#toastContainer').append(toast);
        const toastElement = $('#toastContainer .toast').last();
        const bsToast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
        bsToast.show();
        
        toastElement.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>
@endsection
