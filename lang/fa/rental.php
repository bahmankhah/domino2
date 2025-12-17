<?php

return [
    // ... existing translations ...
    'basic_info' => 'اطلاعات پایه',
    'media' => 'رسانه',
    'description' => 'توضیحات',
    'internal_notes' => 'یادداشت‌های داخلی',
    'created_at' => 'تاریخ ایجاد',
    'updated_at' => 'تاریخ بروزرسانی',
    'name' => 'نام',
    'slug' => 'نامک (Slug)',
    'color' => 'رنگ',
    'is_active' => 'فعال است',
    'days' => 'روز',
    
    // Order / Rent
    'orders_rents' => 'سفارشات (اجاره‌ها)',
    'customer' => 'مشتری',
    'mobile' => 'موبایل',
    'address' => 'آدرس',
    'has_collateral' => 'دارای وثیقه',
    'items' => 'آیتم‌ها',
    'items_count' => 'تعداد آیتم‌ها',
    'good' => 'کالا',
    'duration_type' => 'مدت / نوع',
    'warehouse' => 'انبار',
    'logistic' => 'حمل و نقل',
    'start_date' => 'تاریخ شروع',
    'end_date' => 'تاریخ پایان',
    'price' => 'قیمت',
    'supplier_price' => 'قیمت تامین‌کننده',
    'supplier_price_hint' => 'قیمت تامین‌کننده :amount است',
    'currency' => 'تومان',
    'status' => 'وضعیت',
    'complete_distribute' => 'تکمیل و توزیع درآمد',
    'order_completed_msg' => 'سفارش تکمیل شد و درآمدها توزیع گردید',
    
    // Statuses
    'pending' => 'در انتظار',
    'delivery' => 'در حال ارسال',
    'in_rent' => 'در حال اجاره',
    'completed' => 'تکمیل شده',
    'canceled' => 'لغو شده',

    // Actions
    'mark_delivery' => 'ارسال برای تحویل',
    'mark_in_rent' => 'شروع اجاره',
    'mark_canceled' => 'لغو سفارش',
    'incomes_list' => 'لیست درآمدهای سفارش',

    // Goods
    'goods' => 'کالاها',
    'title' => 'عنوان',
    'code' => 'کد',
    'current_location' => 'موقعیت فعلی',
    'is_available' => 'موجود',

    // New Resources
    'warehouses' => 'انبارها',
    'logistics' => 'لیست لجستیک',
    'categories' => 'دسته‌بندی‌ها',
    'order_types' => 'انواع سفارش',
    
    // Relations / Providers
    'investors_owners' => 'سرمایه‌گذاران / مالکان',
    'warehouse_owners' => 'مالکان انبار',
    'logistic_owners' => 'مالکان لجستیک',
    'user' => 'کاربر',
    'ownership_percent' => 'درصد مالکیت',
    'rent_prices' => 'قیمت‌های اجاره',
    
    // Finance
    'finance' => 'امور مالی',
    'transactions' => 'تراکنش‌ها',
    'balance' => 'موجودی',
    'credit' => 'بستانکار',
    'debit' => 'بدهکار',
    'income' => 'درآمد',
    'expense' => 'هزینه',
    'settlement' => 'تسویه حساب',
    'carrier' => 'حمل و نقل',
    'type' => 'نوع',
    'recipient' => 'دریافت کننده',

    // Income Rule Types
    'good_provider' => 'مالک کالا',
    'warehouse_provider' => 'مالک انبار',
    'logistics_provider' => 'مالک لجستیک (خودرو)',
    'referrer_provider' => 'معرف (بازاریاب)',
    'manage_prices' => 'مدیریت قیمت‌ها',
    'manage_prices_heading' => 'تنظیم قیمت‌ها برای دسته‌بندی',
    'sync_prices_to_goods' => 'همگام‌سازی قیمت‌ها با کالاها',
    'prices_updated_successfully'=> 'قیمت‌ها با موفقیت به‌روزرسانی شدند',
        'order_delivery' => 'تحویل سفارش',
    'order_deliveries' => 'تحویل‌های سفارش',

    'delivered_by' => 'تحویل‌دهنده',
    'delivered_at' => 'تاریخ تحویل',
    'delivery_fee' => 'هزینه تحویل',


    'add_delivery' => 'افزودن تحویل',
    'edit' => 'ویرایش',
    'delete' => 'حذف',
    'delete_selected' => 'حذف موارد انتخاب‌شده',
    'role' => 'نقش',
    'roles'=>[
        'customer' => 'مشتری',
        'admin' => 'مدیر',
        'delivery' => 'تحویل‌دهنده',
    ],

    'driver' => 'بخش راننده',


    'delivery_info' => 'اطلاعات تحویل',
    'delivery_date' => 'تاریخ تحویل',

    'order_items' => 'اقلام سفارش',
    'item_price' => 'قیمت قلم',

    'incomes' => 'درآمدها',
    'add_income' => 'افزودن درآمد',

    'order_id' => 'شناسه سفارش',
    
    // Order Delivery - Driver Section
    'mark_as_delivered' => 'ثبت تحویل',
    'delivery_completed' => 'تحویل کامل شد',
    'order_status_updated_to_in_rent' => 'وضعیت سفارش به "در حال اجاره" تغییر یافت',
    'delivery_marked_as_delivered' => 'تحویل با موفقیت ثبت شد',
    'customer_info' => 'اطلاعات مشتری',
    'customer_name' => 'نام مشتری',
    'customer_mobile' => 'موبایل مشتری',
    'customer_address' => 'آدرس مشتری',
    'not_delivered_yet' => 'هنوز تحویل داده نشده',
    
    // Order Items Widget
    'id' => 'شناسه',
    'order_type' => 'نوع سفارش',
    'started_at' => 'تاریخ شروع',
    'ended_at' => 'تاریخ پایان',
    'total_income' => 'مجموع درآمد',
    'price_rule' => 'قانون قیمت‌گذاری',
    'received_at' => 'تاریخ دریافت',
    'received_by' => 'دریافت‌کننده',
    'income_added' => 'درآمد با موفقیت ثبت شد',
    'view_incomes' => 'مشاهده درآمدها',
    'incomes_for_item' => 'درآمدهای قلم',
    'close' => 'بستن',
    'no_incomes_yet' => 'هنوز درآمدی ثبت نشده است',
    'total' => 'مجموع',
    'amount_to_credit' => 'مبلغ قابل واریز برای تحویل',
    'delivery_income' => 'درآمد تحویل',
    'delivery_incomes_for_item' => 'درآمدهای تحویل برای قلم',
    'no_delivery_incomes_yet' => 'هنوز درآمد تحویلی ثبت نشده است',
    'delivered' => 'تحویل داده شده',
    'cash_from_customer' => 'وجه نقد از مشتری',
    'cash_from_customer_help' => 'اگر راننده تمام پول را از مشتری دریافت کرده است این گزینه را علامت بزنید (هزینه تحویل صفر خواهد شد)',
    'order' => 'سفارش',
    'delivery_assigned' => 'تحویل تخصیص داده شد',
    'delivery_assigned_to_driver' => 'تحویل به راننده تخصیص داده شد',
    'add_manual_income' => 'افزودن درآمد دستی',
    'prefilled_cost' => 'هزینه از پیش تعیین شده',
    'prefilled_cost_help' => 'اگر می‌خواهید زمان و هزینه تحویل را الان تعیین کنید این گزینه را علامت بزنید',
];