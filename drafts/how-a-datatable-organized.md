## Route
```php
Route::resource('/orders', OrderController::class);
Route::match(['get', 'post'], '/order-index-json', [OrderController::class, 'index_json'])->name('order.index_json');
```

# controller 
```php

  public function index()
  {
    $filter_data = [];
    return view('backend::order.index', $filter_data);
  }

  public function index_json()
  {
    $model = (Order::with([
      'insurance_company',
      'user',
      'order_by_cs_team',
      'underwriter',
      'employee',
      'business_by',
      'user.media',
      'underwriter.media',
      'employee.media',
      // 'currently_process_by_users',
      'business_partner',
      'business_partner.employee',
      'orderable',
      'meta',
      'child_orders',
    ])->whereDoesntHave('child_orders')
    );
    $user = Auth::user();
    if ($user->isInsuranceCompany()) {
      // not showing any order to user
      $model = (Order::where('insurance_company_id', 0)->latest()->with('insurance_company'));

      // showing incase he has company and company has order
      if ($user->has_insurance_company()) {
        $model = (Order::where('insurance_company_id', $user->user_insurance_company()->id)

          // ->where('operational_status', 'processing')
          // ->orWhere('operational_status', 'completed')

          ->where(function ($query) {
            $query->where('operational_status', 'processing')
              ->orWhere('operational_status', 'completed');
          })
          ->latest()
          ->with('insurance_company')
        );
      }
    }

    return DataTables::eloquent($model)

      ->filterColumn('name_in_insurance', function ($query, $keyword) {
        $query->whereHas('omc_user_detail', function ($q) use ($keyword) {
          $q->where('name', 'like', "%${keyword}%")
            ->orWhere('passport_no', 'like', "%${keyword}%");
        })->orWhereHas('health_user_detail', function ($q) use ($keyword) {
          $q->where('name', 'like', "%${keyword}%")
            ->orWhere('nid', 'like', "%${keyword}%");
        })->orWhereHas('health_user_detail', function ($q) use ($keyword) {
          $q->where('name', 'like', "%${keyword}%")
            ->orWhere('nid', 'like', "%${keyword}%");
        })->orWhereHas('travel_user_detail', function ($q) use ($keyword) {
          $q->where('name', 'like', "%${keyword}%")
            ->orWhere('identification_number', 'like', "%${keyword}%");
        });
      })
      ->filter(function ($query) {
        if (request()->has('order_ids') && request('order_ids')) {
          $ids_raw = request('order_ids');
          // $ids = array_filter(array_map('trim', explode("\n", $ids_raw)));
          $ids = array_filter(array_map('trim', explode(',', $ids_raw)));
          $query->whereIn('id', $ids);
        }
        // for specific company like green delta insurance
        if (request()->has('insurance_company') && request('insurance_company')) {
          $query->whereIn('insurance_company_id', request('insurance_company'));
        }
        if (request()->has('business_partner') && request('business_partner')) {
          $query->whereIn('business_partner_id', request('business_partner'));
        }
        if (request()->has('agent') && request('agent')) {
          $query->whereIn('agent_id', request('agent'));
        }
        if (request()->has('product_type') && request('product_type')) {
          $query->whereIn('product_type', request('product_type'));
        }
        if (request()->has('product_type_badge') && request('product_type_badge')) {
          $query->whereIn('product_type', request('product_type_badge'));
        }

        if (request()->has('operational_status') && request('operational_status')) {
          $query->whereIn('operational_status', request('operational_status'));
        }
        if (request()->has('payment_status') && request('payment_status')) {
          $query->whereIn('payment_status', request('payment_status'));
        }
        if (request()->has('payment_method') && request('payment_method')) {
          $query->whereIn('payment_method', request('payment_method'));
        }
        if (request()->has('source') && request('source')) {
          $query->whereIn('source', request('source'));
        }
        if (request()->has('starting_date_of_order_create_at') && request('starting_date_of_order_create_at')) {
          $query->whereDate('created_at', '>=', request('starting_date_of_order_create_at'));
        }
        if (request()->has('ending_date_of_order_created_at') && request('ending_date_of_order_created_at')) {
          $query->whereDate('created_at', '<=', request('ending_date_of_order_created_at'));
        }
        if (request()->has('starting_date_of_order_underwriting_date') && request('starting_date_of_order_underwriting_date')) {
          $query->whereDate('underwriting_date', '>=', request('starting_date_of_order_underwriting_date'));
        }
        if (request()->has('ending_date_of_order_underwriting_date') && request('ending_date_of_order_underwriting_date')) {
          $query->whereDate('underwriting_date', '<=', request('ending_date_of_order_underwriting_date'));
        }
        if (request()->has('underwriting_date') && request('underwriting_date')) {
          $query->whereDate('underwriting_date', request('underwriting_date'));
        }
        if (request()->has('business_by_employee') && request('business_by_employee')) {
          $business_partner_ids = BusinessPartner::whereIn('employee_id', request('business_by_employee'))->pluck('id');
          $query->whereIn('business_partner_id', $business_partner_ids);
        }
        if (request()->has('order_by_employee') && request('order_by_employee')) {
          if (in_array('blank_id', request('order_by_employee'))) {
            $query->whereNull('order_by_cs_id');
          } else {
            $query->whereIn('order_by_cs_id', request('order_by_employee'));
          }
        }
        if (request()->has('policy_certificate_number') && request('policy_certificate_number')) {
          $query->whereMeta('policy_certificate_number', request('policy_certificate_number'));
        }
        if (request()->has('referral_source') && request('referral_source')) {
          $query->whereIn('referral_source', request('referral_source'));
        }
        if (request()->has('discounts') && request('discounts')) {
          foreach (request('discounts') as $key => $discount) {
            if ($key > 0) {
              $query->orWhere($discount, '>', 0);
            } else {
              $query->where($discount, '>', 0);
            }
          }
        }
        if (request()->has('with_or_without_business_partner') && request('with_or_without_business_partner')) {
          $with_or_without_business_partner = request('with_or_without_business_partner');
          $business_partner_ids = Cache::remember(Str::slug('OrderController index_json_filter with_or_without_business_partner BusinessPartner'), 60 * 10, function () {
            return BusinessPartner::query()->where('type', 'company')->pluck('id');
          });
          if ($with_or_without_business_partner == 'orders_with_business_partners') {
            $query->whereIn('business_partner_id', $business_partner_ids);
          }
          if ($with_or_without_business_partner == 'orders_with_out_business_partners') {
            $query->where(function ($query) use ($business_partner_ids) {
              $query->whereNotIn('business_partner_id', $business_partner_ids);
              $query->orWhereNull('business_partner_id');
            });
          }
        }
      }, true)
      ->addColumn('insurance_company', function (Order $order) {
        return $order->insurance_company?->name;
        if ($order->insurance_company) {
          $insurance_company = $order->insurance_company->name;
          $insurance_company = htmlspecialchars($insurance_company);
          $insurance_company_sub_string = substr($insurance_company, 0, 15);
        } else {
          $insurance_company = 'No company assigned';
          $insurance_company_sub_string = 'N/A';
        }
        $html_fragments = sprintf(
          "
                          <span
                              data-toggle='tooltip'
                              data-placement='top'
                              title='%s'
                          >%s..</span>",
          htmlspecialchars($insurance_company),
          htmlspecialchars($insurance_company_sub_string)
        );

        return $html_fragments;
      })
      ->addColumn('enrollment_ids', function (Order $order) {
          $enrollmentIds = $order->enrollments()
        ->withoutGlobalScopes()
        ->get()
        ->pluck('id')
        ->implode(',');

        return strlen($enrollmentIds) > 40 
            ? substr($enrollmentIds, 0, 40) . '...' 
            : $enrollmentIds;
      })
      ->addColumn('business_partner', function (Order $order) {
        return $order->business_partner?->name;
        if ($order->business_partner) {
          $business_partner = $order->business_partner->name;
          $business_partner = htmlspecialchars($business_partner);
          $business_partner_sub_string = substr($business_partner, 0, 15);
        } else {
          $business_partner = 'No company assigned';
          $business_partner_sub_string = 'N/A';
        }
        $html_fragments = sprintf(
          "
          <span
              data-toggle='tooltip'
              data-placement='top'
              title='%s'
          >%s..</span>",
          htmlspecialchars($business_partner),
          htmlspecialchars($business_partner_sub_string)
        );

        return $html_fragments;
      })
      ->addColumn('user_email', function (Order $order) {
        if ($order->user) {
          return $order->user->email;
        }
        return 'email not found';
      })
      ->addColumn('employee_name', function (Order $order) {
        if ($order->employee) {
          return $order->employee->name;
        }
        return '';
      })
      ->addColumn('business_by', function (Order $order) {
        $business_by = $order->business_by ?? $order->business_partner?->employee;
        if ($business_by) {
          return sprintf(
            '<a class="btn btn-success" href="%s">%s</a>',
            route('backend.business_by_orders', ['employee' => $business_by->id]),
            $business_by->name
          );
        }
        return '';
      })
      ->addColumn('underwriter_name', function (Order $order) {
        if ($order->underwriter) {
          return $order->underwriter->name;
        }
        return '';
      })
      ->addColumn('name_in_insurance', function (Order $order) {
        if ($order->orderable) {
          if ($order->orderable->name) {
            return $order->orderable->name;
          }
        }
        return '';
      })
      ->addColumn('pgw_discount_reference', function (Order $order) {
        $pgw_discount = $order->pgw_discount;

        $discount_remarks = [];

        $payment_references = $order->payment_reference ? json_decode($order->payment_reference, true) : [];

        if (is_array($payment_references)) {
          foreach ($payment_references as $reference) {
            if (isset($reference['discount_remarks'])) {
              $discount_remarks[] = $reference['discount_remarks'];
            }
          }
        }

        if (count($discount_remarks)) {
          $discount_remarks = implode(', ', $discount_remarks);
          return $pgw_discount . "<br><small>{$discount_remarks}</small>";
        }

        return $pgw_discount;
      })
      ->editColumn('payment_reference', function (Order $order) {
        if ($order->payment_reference) {
          return substr($order->payment_reference, 0, '60');
        }
        return null;
      })
      ->addColumn('registration_number', function (Order $order) {
        if ($order->product_type == 'act_liability' && $order->orderable) {
          if ($order->orderable->registration_number) {
            return $order->orderable->registration_number;
          }
        }
        return '';
      })
      ->addColumn('countries', function (Order $order) {
        if ($order->product_type == 'omc' && $order->orderable) {
          $countries = json_decode($order->orderable->countries);
          $countries = implode(', ', $countries);
          return $countries;
        }
        return '';
      })->addColumn('policy_duration', function (Order $order) {
        if ($order->product_type == 'omc' && $order->orderable) {
          if ($order->orderable->days) {
            return $order->orderable->days . ' days';
          }
        }
        if ($order->product_type == 'travel' && $order->orderable) {
          if ($order->orderable->policy_duration_days) {
            return $order->orderable->policy_duration_days . ' days';
          }
        }
        return '';
      })
      ->addColumn('identification_number', function (Order $order) {
        if ($order->product_type == 'omc' && $order->orderable) {
          if ($order->orderable->passport_no) {
            return sprintf('passport: %s', $order->orderable->passport_no);
          }
        }
        if ($order->product_type == 'health' && $order->orderable) {
          if ($order->orderable->nid) {
            return sprintf('Passport / Nid: %s', $order->orderable->nid);
          }
        }
        if ($order->product_type == 'travel' && $order->orderable) {
          if ($order->orderable?->identification_type && $order->orderable?->identification_number) {
            return sprintf(
              '%s: %s',
              $order->orderable?->identification_type,
              $order->orderable?->identification_number,
            );
          }
        }
        return '';
      })
      ->addColumn('policy_certificate_number', function (Order $order) {
        return $order->getMeta('policy_certificate_number') ?? $order->orderable?->policy_number ?? '';
      })
      ->addColumn('id_formatted', function (Order $order) {
        $url = "<a class='btn btn-link' href='" . route('backend.orders.show', $order->id) . "'><b>{$order->id}</b></a>";

        if ($order->child_orders->count()) {
          foreach ($order->child_orders as $child_order) {
            $url .= "<br><a class='btn btn-link' href='" . route('backend.orders.show', $child_order->id) . "'>{$child_order->id}</a>";
          }
        }

        return $url;
      })
      ->addColumn('action_tab', function (Order $order) {
        $html_fragments = sprintf(
          "<a target='_blank' class='btn btn-info' href='%s'>View Details</a>",
          route('backend.orders.show', $order->id)
        );
        return $html_fragments;
      })
      ->addColumn('parcel_id_btn', function (Order $order) {
        if ($order->parcel_id) {
          $redx_url = "https://redx.com.bd/track-parcel/?trackingId=$order->parcel_id&shopId=539842";
          return sprintf(
            "<a target='_blank' class='btn btn-primary' href='%s'>%s</a>",
            $redx_url,
            $order->parcel_id
          );
        }
        return null;
      })
      ->addColumn('invoice_order_button', function (Order $order) {

        if ($order->invoice_order_id) {
          $invoice_order_button_link = route('order::invoice_orders.show', $order->invoice_order_id);

          return sprintf(
            "<a target='_blank' class='btn btn-primary' href='%s'>%s</a>",
            $invoice_order_button_link,
            $order->invoice_order_id
          );
        }
        return "";
      })
      ->addColumn('order_by_employee', function (Order $order) {
        return $order->order_by_cs_team?->name;
      })
      ->addColumn('created_at_formatted', function (Order $order) {
        return Helpers::getDateAndDiff($order->created_at, true, true, true);
      })
      ->addColumn('payment_date_formatted', function (Order $order) {
        return Helpers::getDateAndDiff($order->payment_date, true, true, true);
      })
      ->addColumn('processing_date_formatted', function (Order $order) {
        return Helpers::getDateAndDiff($order->processing_date, true, false);
      })
      ->addColumn('underwriting_date_formatted', function (Order $order) {
        return Helpers::getDateAndDiff($order->underwriting_date, true, false);
      })
      ->addColumn('policy_start_date_formatted', function (Order $order) {
        return Helpers::getDateAndDiff($order->policy_start_date, true, false);
      })
      ->addColumn('operational_status_btn', function (Order $order) {
        $status = Helpers::toHeadline($order->operational_status);
        $html_fragments = sprintf(
          "<span class='status_btn status__%s'>%s</span>",
          $order->operational_status,
          $status
        );
        return $html_fragments;
      })
      ->editColumn('payment_status', function (Order $order) {
        $status = Helpers::toHeadline($order->payment_status);
        $html_fragments = sprintf(
          " <span class='status_btn status__%s'>%s</span> ",
          $order->payment_status,
          $status
        );
        return $html_fragments;
      })
      ->addColumn('delivery_status_btn', function (Order $order) {
        $status = $order->delivery_status;
        $btn_class = match (strtolower($status)) {
          'pending'   => 'btn btn-secondary',
          'delivered' => 'btn btn-success',
          'shipped'   => 'btn btn-info',
          default    => 'btn',
        };
        $html_fragments = sprintf(
          "<span class='%s'>%s</span>",
          $btn_class,
          strtoupper($status),
        );
        return $html_fragments;
      })
      ->addColumn('order_warning', function (Order $order) {
        $warning_data = Helpers::getOrderWarning($order);
        $warning_type = $warning_data['type'];
        $warning_message = $warning_data['message'];

        $div_class = match ($warning_type) {
          'danger'   => 'alert alert-danger',
          'warning' => 'alert alert-warning',
          'secondary'   => 'alert alert-secondary',
          'primary'   => 'alert alert-primary',
          default    => 'alert alert-secondary',
        };
        $html_fragments = sprintf(
          "<div class='%s'>%s</div>",
          $div_class,
          $warning_message,
        );
        return $html_fragments;
      })
      ->editColumn('product_type_detail', function (Order $order) {
        return Helpers::readableProductType($order);
      })
      ->rawColumns([
        'id_formatted',
        'action',
        'business_partner',
        'action_tab',
        'parcel_id_btn',
        'insurance_company',
        'operational_status_btn',
        'delivery_status_btn',
        'order_warning',
        'payment_status',
        'processing_date',
        'business_by',
        'invoice_order_button',
        'pgw_discount_reference',
      ])
      ->toJson();
  }
```


## view file 
```html
@extends('ui-backend::layouts.app')
@section('content')
    <div class="px-2 bg-white motor-view-page">
        @include('backend.partials.alert')
        @include('backend.partials.errors')
        @include('backend.orders.partials._order_index_filter')
        <div id="datatable-info-custom" class="mb-3"></div>
        <div class="table-responsive vertical-drag" id="all_orders_responsive">
            <table class="table table-bordered" id="order-table">
                <thead>
                    <th>ID</th>
                    <th>Order Date</th>
                    <th>Business By</th>
                    <th>Details</th>
                    <th>O. Status</th>
                    <th>P. Status</th>
                    <th>D. Status</th>
                    <th>Source</th>
                    <th>P. Method</th>
                    <th>Product Type</th>
                    <th>Product Category</th>
                    <th>Net Sales Revenue</th>
                    <th>Invoice ID</th>
                    <th>Enrollment IDs</th>
                    <th>Client Name</th>
                    <th>Insured Name</th>
                    <th>Processed By</th>
                    <th>Underwriting By</th>
                    <th>Order By Employee</th>

                    <th>Net Premium</th>
                    <th>VAT</th>
                    <th>SD</th>
                    <th>Gross Premium</th>
                    <th>Gross Sales Revenue</th>
                    <th>Platform Discount</th>
                    <th>Trade Discount</th>
                    <th>PGW Discount</th>
                    <th>Total Discount</th>
                    <th>Cashback</th>
                    <th>Sourcing Price</th>
                    <th>Contribution</th>
                    <th>Prepayment Amount</th>
                    <th>PGW Transaction ID</th>
                    <th>Payment Reference</th>
                    <th>Business Partner</th>
                    <th>Issuing Company</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>User Email</th>
                    <th>Underwriting Date</th>
                    <th>Identification Number</th>
                    <th>Policy Number</th>
                    <th>Policy Duration</th>
                    <th>Policy Start Date</th>
                    <th>Actual Received Amount</th>
                    <th>Payment date</th>
                    <th>Referral Source</th>
                    <th>Referral Code</th>
                </thead>
            </table>

            <div class="row datable-goto-page-block">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="number" value="1" class="form-control" id="datatable-page-number">
                        <button id="datatable-page-number-button" class="btn btn-outline-secondary" type="button">Go To
                            Page Number</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection



@push('scripts')

    <script>
        $(document).ready(function() {

            let activeProductTypeBadges = [];

            function updateDataTable() {
                orderDataTable.draw();
            }

            function updateBadgeStatus() {
                $('.product-type-badge').each(function() {
                    const productType = $(this).data('product-type');
                    if (activeProductTypeBadges.includes(productType)) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            }

            $('.product-type-badge').on('click', function() {
                const productType = $(this).data('product-type');
                const index = activeProductTypeBadges.indexOf(productType);

                if (index > -1) {
                    activeProductTypeBadges.splice(index, 1);
                } else {
                    activeProductTypeBadges.push(productType);
                }

                updateBadgeStatus();
                updateDataTable();
            });

            $('.product-type-badge').on('dblclick', function() {
                const productType = $(this).data('product-type');
                const index = activeProductTypeBadges.indexOf(productType);

                if (index > -1) {
                    activeProductTypeBadges.splice(index, 1);
                    updateBadgeStatus();
                    updateDataTable();
                }
            });



            var columns = [{
                    data: 'id_formatted',
                    name: 'id',
                    width: '200px'
                },
                {
                    data: 'created_at_formatted',
                    name: 'created_at',
                    searchable: false,
                },
                {
                    data: 'business_by',
                    name: 'business_by',
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    width: '200px'
                },
                {
                    data: 'operational_status_btn',
                    name: 'operational_status',
                    searchable: false,
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    searchable: false,
                },
                {
                    data: 'delivery_status_btn',
                    name: 'delivery_status',
                    searchable: false,
                },
                {
                    data: 'source',
                    name: 'source',
                    width: '200px'
                },
                {
                    data: 'payment_method',
                    name: 'payment_method',
                    searchable: false,
                },
                {
                    data: 'product_type_detail',
                    name: 'product_type_detail',
                    width: '200px'
                },
                {
                    data: 'product_category',
                    name: 'product_category',
                    width: '200px'
                },
                {
                    data: 'net_sales_revenue',
                    name: 'net_sales_revenue',
                    searchable: true,
                },
                {
                    data: 'invoice_order_button',
                    searchable: true,
                    name: 'invoice_order_id',
                    width: '200px'
                },
                {
                    data: 'enrollment_ids',
                    searchable: false,
                    name: 'id',
                    width: '200px'
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '200px'
                },
                {
                    data: 'name_in_insurance',
                    name: 'name_in_insurance',
                    width: '200px'
                },
                {
                    data: 'employee_name',
                    name: 'employee_name',
                    searchable: false,
                    width: '200px'
                },
                {
                    data: 'underwriter_name',
                    name: 'underwriter_name',
                    searchable: false,
                    width: '200px'
                },
                {
                    data: 'order_by_employee',
                    name: 'order_by_employee',
                    searchable: false,
                    width: '200px'
                },


                {
                    data: 'net_premium',
                    name: 'net_premium',
                    searchable: true,
                },
                {
                    data: 'vat',
                    name: 'vat',
                    searchable: false
                },
                {
                    data: 'sd',
                    name: 'sd',
                    searchable: false
                },
                {
                    data: 'gross_premium',
                    name: 'gross_premium',
                    searchable: true,
                },
                {
                    data: 'gross_sales_revenue',
                    name: 'gross_sales_revenue',
                    searchable: false
                },
                {
                    data: 'platform_discount',
                    name: 'platform_discount',
                    searchable: false,
                },
                {
                    data: 'trade_discount',
                    name: 'trade_discount',
                    searchable: false,
                },
                {
                    data: 'pgw_discount_reference',
                    name: 'pgw_discount_reference',
                    searchable: false,
                },
                {
                    data: 'total_discount',
                    name: 'total_discount',
                    searchable: false,
                },
                {
                    data: 'cashback',
                    name: 'cashback',
                    searchable: false,
                },
                {
                    data: 'sourcing_price',
                    name: 'sourcing_price',
                    searchable: false,
                },
                {
                    data: 'contribution',
                    name: 'contribution',
                    searchable: false,
                },

                {
                    data: 'prepayment_amount',
                    name: 'prepayment_amount',
                    searchable: false,
                },
                {
                    data: 'pgw_transaction_id',
                    searchable: true,
                    name: 'pgw_transaction_id',
                },
                {
                    data: 'payment_reference',
                    searchable: true,
                    name: 'payment_reference',
                },
                {
                    data: 'business_partner',
                    name: 'business_partner.name',
                    searchable: false,
                    width: '200px'
                },
                {
                    data: 'insurance_company',
                    name: 'insurance_company.name',
                    searchable: false,
                    width: '200px'
                },
                {
                    data: 'mobile',
                    name: 'mobile',
                    width: '200px'
                },
                {
                    data: 'email',
                    name: 'email',
                    width: '200px'
                },
                {
                    data: 'user_email',
                    name: 'user_email',
                    width: '200px'
                },
                // { data: 'processing_date_formatted', name: 'processing_date', width: '200px', searchable: false,},
                {
                    data: 'underwriting_date_formatted',
                    name: 'underwriting_date',
                    searchable: false,
                },
                {
                    data: 'identification_number',
                    name: 'identification_number',
                    searchable: false
                },
                {
                    data: 'policy_certificate_number',
                    name: 'policy_certificate_number',
                    width: '200px'
                },
                {
                    data: 'policy_duration',
                    name: 'policy_duration',
                    searchable: false
                },
                {
                    data: 'policy_start_date_formatted',
                    name: 'policy_start_date_formatted',
                    searchable: false
                },

                {
                    data: 'actual_received_amount',
                    searchable: false,
                    name: 'actual_received_amount',
                    width: '200px'
                },
                {
                    data: 'payment_date_formatted',
                    searchable: false,
                    name: 'payment_date',
                    width: '200px'
                },
                {
                    data: 'referral_source',
                    searchable: false,
                    name: 'referral_source',
                    width: '200px'
                },
                {
                    data: 'referral_code',
                    searchable: false,
                    name: 'referral_code',
                    width: '200px'
                },
            ];
            var data_table_args = {
              
                processing: true,
                serverSide: true,
                searchDelay: 500,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 300, 500, 1000, 1300, 1500 ],
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass(data['operational_status']);
                },
                ajax: {
                    url: '{!! route('json.order.index_filter') !!}',
                    type: "POST",
                    data: function(d) {
                        d.insurance_company = $('#insurance_company').val();
                        d.business_by_employee = $('#business_by_employee').val();
                        d.order_by_employee = $('#order_by_employee').val();
                        d.policy_certificate_number = $('#policy_certificate_number').val();
                        d.referral_source = $('#referral_source').val();
                        d.discounts = $('#discounts').val();
                        d.business_partner = $('#business_partner').val();
                        d.agent = $('#agent').val();
                        d.product_type = $('#product_type').val();
                        d.product_type_badge = activeProductTypeBadges;
                        d.operational_status = $('#operational_status').val();
                        d.payment_status = $('#payment_status').val();
                        d.payment_method = $('#payment_method').val();
                        d.search_text = $('#search_text').val();
                        d.source = $('#source').val();

                        d.starting_date_of_order_create_at = $('#starting_date_of_order_create_at').val();
                        d.ending_date_of_order_created_at = $('#ending_date_of_order_created_at').val();
                        d.starting_date_of_order_underwriting_date = $(
                            '#starting_date_of_order_underwriting_date').val();
                        d.ending_date_of_order_underwriting_date = $(
                            '#ending_date_of_order_underwriting_date').val();

                        d.underwriting_date = $('#underwriting_date').val();
                        d.with_or_without_business_partner = $('#with_or_without_business_partner').val();
                        d.order_ids = $('#order_ids').val();
                        // console.log('d', d);
                    }
                },
                "autoWidth": false,
                columns: columns,
                order: [
                    [1, 'desc'],
                ],
                "ordering": true,
            };

            // make button active only for admin and developer
            @anySimpleRoles(['developer', 'admin', 'accounts'])
            data_table_args.buttons = [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ];
            data_table_args.dom = 'Blifrtip'; // Blfrtip, Blifrtip
            @endanySimpleRoles

            var orderDataTable = $('#order-table').DataTable(data_table_args);

            dataTableNavigate(orderDataTable);






            

            $date_fields = [
                "#payment_date",
                "#payment_date_update",
                "#created_at",
                "#underwriting_date",
                "#starting_date_of_order_create_at",
                "#ending_date_of_order_created_at",
                "#starting_date_of_order_underwriting_date",
                "#ending_date_of_order_underwriting_date",
            ];
            $date_fields.forEach(function(item) {
                $(item).flatpickr({
                    dateFormat: "Y-m-d",
                    onReady: function(dateObj, dateStr, instance) {
                        var $cal = $(instance.calendarContainer);
                        if ($cal.find('.flatpickr-clear').length < 1) {
                            $cal.append('<div class="flatpickr-clear">Clear</div>');
                            $cal.find('.flatpickr-clear').on('click', function() {
                                instance.clear();
                                instance.close();
                            });
                        }
                    }
                });
            })

            // Initialize date range pickers
            $("#order_creation_date_range").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                        $("#starting_date_of_order_create_at").val(startDate);
                        $("#ending_date_of_order_created_at").val(endDate);
                        orderDataTable.draw();
                    }
                },
                onReady: function(dateObj, dateStr, instance) {
                    var $cal = $(instance.calendarContainer);
                    if ($cal.find('.flatpickr-clear').length < 1) {
                        $cal.append('<div class="flatpickr-clear">Clear</div>');
                        $cal.find('.flatpickr-clear').on('click', function() {
                            instance.clear();
                            instance.close();
                            $("#starting_date_of_order_create_at").val('');
                            $("#ending_date_of_order_created_at").val('');
                            orderDataTable.draw();
                        });
                    }
                }
            });

            $("#underwriting_date_range").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
                        $("#starting_date_of_order_underwriting_date").val(startDate);
                        $("#ending_date_of_order_underwriting_date").val(endDate);
                        orderDataTable.draw();
                    }
                },
                onReady: function(dateObj, dateStr, instance) {
                    var $cal = $(instance.calendarContainer);
                    if ($cal.find('.flatpickr-clear').length < 1) {
                        $cal.append('<div class="flatpickr-clear">Clear</div>');
                        $cal.find('.flatpickr-clear').on('click', function() {
                            instance.clear();
                            instance.close();
                            $("#starting_date_of_order_underwriting_date").val('');
                            $("#ending_date_of_order_underwriting_date").val('');
                            orderDataTable.draw();
                        });
                    }
                }
            });

            var id_change_listeners_for_order_table = [
                '#insurance_company',
                '#business_partner',
                '#business_by_employee',
                '#order_by_employee',
                '#agent',
                '#product_type',
                '#operational_status',
                '#payment_status',
                '#payment_method',
                '#source',
                '#starting_date_of_order_create_at',
                '#ending_date_of_order_created_at',
                '#starting_date_of_order_underwriting_date',
                '#ending_date_of_order_underwriting_date',
                '#underwriting_date',
                '#starting_date_of_order_underwriting_date',
                '#ending_date_of_order_underwriting_date',
                '#with_or_without_business_partner',

                '#referral_source',
                '#discounts',
            ];


            id_change_listeners_for_order_table.forEach(function(item) {
                // for each change
                $(item).change(function(e) {
                    orderDataTable.draw();
                    e.preventDefault();
                });
            })



            function updateDataTableInfo() {
                $('#datatable-info-custom').text($('.dataTables_info').text());
            }
            $('.dataTables_info').on("DOMSubtreeModified", function() {
                updateDataTableInfo();
            });
            updateDataTableInfo();



        });
    </script>
@endpush

```

## global js for datatable 

```js
// for datatable pageNumber navigate
  function dataTableNavigate(dataTableName) {
    var params_for_datatable_page_number = new URLSearchParams(window.location.search)
    var dt_page_number = parseInt(params_for_datatable_page_number.get('page'))
    if (dt_page_number) {
      dt_page_number--
      dataTableName.on('init.dt', function(e) {
        dataTableName.page(dt_page_number).draw(false);
      });
    }

    const searchURL = new URL(window.location);
    dataTableName.on('draw.dt', function() {
      var info = dataTableName.page.info();
      searchURL.searchParams.set('page', (info.page + 1));
      window.history.replaceState({}, '', searchURL);
    });

    // Go to Datatable page
    $('#datatable-page-number-button').on('click', function() {
      var page_number = parseInt($('#datatable-page-number').val());
      if (page_number) {
        page_number--
        dataTableName.page(page_number).draw(false);
      }
    })
  } // ending dataTableNavigate fn
```


