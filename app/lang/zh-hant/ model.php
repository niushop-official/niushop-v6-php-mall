<?php

return [
    //基礎返回值
    'SUCCESS' => '操作成功' ,
    'FAIL' => '操作失敗' ,
    'SAVE_SUCCESS' => '保存成功' ,
    'SAVE_FAIL' => '保存失敗' ,
    'REQUEST_SUCCESS' => '請求成功' ,
    'REQUEST_FAIL' => '請求失敗' ,
    'DELETE_SUCCESS' => '刪除成功' ,
    'DELETE_FAIL' => '刪除失敗' ,
    'UNKNOW_ERROR' => '未知錯誤' ,
    'PARAMETER_ERROR' => '參數錯誤' ,
    'REQUEST_SITE_ID' => '缺少必須參數網站id' ,
    'REQUEST_APP_MODULE' => '缺少必須參數應用模組' ,

    //運費
    'TEMPLATE_TO_LONG' => '每個網站最多添加10條運費範本' ,
    'TEMPLATE_EMPTY' => '為設置完善配送方式' ,
    'TEMPLATE_AREA_EXIST' => '當前收貨位址不支持配送' ,
    //商品
    'REQUEST_GOODS_ATTRIBUTE_ID' => '缺少必須參數商品類型id' ,
    //會員相關
    'MEMBER_NOT_EXIST' => '會員不存在' ,
    'MEMBER_IS_LOCKED' => '會員被鎖定' ,
    'USERNAME_EXISTED' => '用戶名已存在' ,
    'MOBILE_EXISTED' => '手機號碼已存在' ,
    'EMAIL_EXISTED' => '郵箱已存在' ,
    'REGISTER_REFUND' => '未開放註冊' ,
    'USERNAME_OR_PASSWORD_ERROR' => '用戶名或密碼錯誤' ,
    //消息管理
    'REQUEST_KEYWORDS' => '缺少必要資訊關鍵字' ,
    'EMPTY_SMS_TYPE' => '沒有可用的簡信發送方式' ,
    'SMS_FAIL' => '簡信發送失敗' ,
    'SMS_SUCCESS' => '簡信發送成功' ,
    'EMAIL_FAIL' => '郵件發送失敗' ,
    'EMAIL_SUCCESS' => '郵件發送成功' ,
    //訂單
    "COUPON_ERROR" => '優惠券不存在或者已經使用' ,
    "ORDER_DELIVERY_CODE_ERROR" => '訂單提貨碼錯誤' ,
    "ORDER_EMPTY" => '訂單不存在' ,
    "ORDER_LOCK" => '訂單已被鎖定' ,
    "ORDER_GOODS_IS_REFUND" => '存在已退款的訂單項' ,
    "ORDER_GOODS_EMPTY" => '訂單項不存在' ,
    "ORDER_GOODS_IS_DELIVERYED" => '訂單項已發貨' ,
    //店鋪
    "MEMBER_SHOP_BIND_EXISTED" => '會員已經申請店鋪或者已入駐' ,
    "APPLY_EXISTED" => '商家申請已存在' ,
    "SHOP_EXISTED" => '店鋪已存在' ,
    "NOT_SUPPORT_SHOP_WITHDRAW" => '系統不支援手動轉帳' ,
    "SHOP_APPLY_MONEY_NOT_ENOUGH" => '申請金額超過了帳戶金額' ,
    //基礎系統
    'REQUEST_CONFIG_KEY' => '缺少必須參數config key' ,
    'REQUEST_DOCUMENT_KEY' => '缺少必須參數document key' ,
    'CONFIG_NOT_EXIST' => '配置不存在,無法開啟' ,
    //外掛程式安裝與卸載
    'ADDON_NOT_EXIST' => '外掛程式不存在' ,
    'ADDON_IS_EXIST' => '外掛程式已經存在' ,
    'ADDON_INFO_ERROR' => '外掛程式資訊有誤，請檢查資訊缺失或外掛程式標識重複' ,
    'ADDON_INSTALL_MENU_FAIL_EXISTED' => '安裝功能表失敗：菜單已存在' ,
    'ADDON_INSTALL_MENU_FAIL' => '安裝功能表失敗' ,
    'ADDON_INSTALL_FAIL' => '安裝外掛程式失敗' ,
    'ADDON_ADD_FAIL' => '安裝外掛程式失敗：寫入外掛程式資料失敗' ,
    'ADDON_UNINSTALL_FAIL' => '執行卸載失敗' ,
    'ADDON_UNINSTALL_MENU_FAIL' => '執行卸載失敗' ,
    //資料庫
    'DABASE_REPAIR_FAIL' => '資料庫修復失敗' ,
    'DATABASE_OPTIMIZE_FAIL' => '資料表優化失敗' ,
    'REQUEST_DATABASE_TABLE' => '請指定要選擇的資料表' ,
    //用戶
    'USER_EXISTED' => '用戶已存在' ,
    'USER_NOT_EXIST' => '用戶不存在' ,
    'USER_IS_LOCKED' => '用戶已被鎖定' ,
    'PASSWORD_ERROR' => '使用者密碼錯誤' ,
    'PERMISSION_DENIED' => '當前用戶沒有許可權' ,
    'USER_GROUP_NOT_ALL_DELETE' => '用戶組不能批量刪除' ,
    'USER_GROUP_USED' => '存在使用當前用戶組的用戶,不可刪除!' ,
    'USER_LOGIN_ERROR' => '帳號或密碼錯誤' ,

    'CAPTCHA_FAILURE' => '驗證碼已失效' ,
    'CAPTCHA_ERROR' => '驗證碼不正確' ,
    //上傳
    'UPLOAD_SUCCESS' => '上傳成功' ,
    'ALBUM_DELETE_FAIL_BY_PIC' => '當前刪除相冊中存在圖片,不可刪除!' ,
    'ALBUM_DELETE_FAIL_BY_DEFAULT' => '當前刪除相冊中存在默認相冊,默認相冊不可刪除!' ,
    'SIGNED_IN' => '您已簽到' ,
    'PAY_PASSWORD_ERROR' => '支付密碼錯誤' ,
    'OLD_PAY_PASSWORD_ERROR' => '原支付密碼錯誤' ,
    'RESULT_ERROR' => '返回結果錯誤' ,
    'UPLOAD_TYPE_ERROR' => '上傳格式有誤' ,

    //核銷
    'VERIFIER_FAIL' => '當前核銷員沒有核銷許可權' ,
    'IS_VERIFYED' => '核銷碼已被使用' ,

    // 消息通知
    'NOT_SET_SMS_TEMPLATE' => '商家未配置該範本' ,
    'SMS_AMOUNT' => '簡信餘額不足，請聯繫店家' ,
    'MESSAGE_FAIL' => '消息發送失敗',

    // 行銷活動
    'GOODS_EXIST_MANJIAN' => '有商品已存在滿減活動',

    // 小程式發佈
    'RELEASE_SUCCESS' => '發佈成功',
    'CANCEL_SUCCESS' => '審核撤回成功',

    //會員帳戶
    'ACCOUNT_EMPTY' => '帳戶餘額不足',
    'MOBILE_EXISTS' => '該手機號已存在'
];

