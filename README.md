# Connect Magento with SHOPFLIX
Magento 2.4.0 | Magento 2.4.1 | Magento 2.4.2 | Magento 2.4.3
:------------ | :-------------| :-------------| :-------------
:heavy_check_mark: | :heavy_check_mark: |  :heavy_check_mark: | :heavy_check_mark:


This extension is connecting your Magento 2 with [SHOPFLIX](https://SHOPFLIX.gr)

## 1. How to install Onecode_ShopFlixConnector

### 1.1 Install via composer

```
composer require onecode/shopflix-connector
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento setup:di:compile
```

### 1.2 Upgrade via composer

```
composer update onecode/shopflix-connector
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

Run compile if you have your application in Production Mode

```
php bin/magento setup:di:compile
```

### 1.3 Copy and paste

If you don't want to install via composer, you can use this way.

- Download [the latest version here](https://github.com/OnecodeGr/shopflix-connector/archive/master.zip)
- Extract `master.zip` file to `app/code/Onecode/ShopFlixConnector` ; You should create a folder
  path `app/code/Onecode/ShopFlixConnector` if not exist.
- Require ShopFlixConnectoLibrary before installation run the command ``composer require onecode/shopflix-connector-library``
- Require Onecode_Base before installation run the command ``composer require onecode/base`` or Download [the latest](https://github.com/OnecodeGr/base/archive/master.zip). Extract `master.zip` file to `app/code/Onecode/Base`  ; You should create a folder
  path `app/code/Onecode/Base` if not exist.
- Go to Magento root folder and run upgrade command line to install `Onecode_ShopFlixConnector`:
```
php bin/magento module:enable Onecode_ShopFlixConnector
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

Run compile if you have your application in Production Mode

```
php bin/magento setup:di:compile
```

## 2. Magento 2 SHOPFLIX extension

In this guide we will show you how to configure the extension and use it.

### 2.1 Configuration

Login to the **Magento Admin**, navigate to `Store > Configuration > Onecode Extensions > SHOPFLIX`

![Imgur](https://i.imgur.com/OE52Qhi.gif)

#### 2.1.1 General

**Enable**: Select `Yes` to activate the module and No to disable it.

![Imgur](https://i.imgur.com/n1Iz7YT.png)

**Convert to magento order**: Select `Yes` to convert order to magento order after accepting the order

![Imgur](https://i.imgur.com/0XvscwO.png)

**Auto accept order**: Select `Yes` if you want to auto accept the order if you have the requested qty

![Imgur](https://i.imgur.com/qSzA1Eh.png)

**API URL / Username / API Key**: These fields are provided by [SHOPFLIX](https://SHOPFLIX.gr)

![Imgur](https://i.imgur.com/OC9WgCY.png)

**Product type to export on xml**: Select which product types you can export on xml

![Imgur](https://i.imgur.com/cg5v1Br.png)

#### 2.1.2 Xml Config

**Generate Xml**: Select `Yes` if you want the extension generate the xml files

![Imgur](https://i.imgur.com/SJ72Tk0.png)

**Export Category Tree**: Select `Yes` if you want to export the categories in xml

![Imgur](https://i.imgur.com/1oW23SQ.png)

**MPN Attribute**: Select the MPN attribute from your system for xml

![Imgur](https://i.imgur.com/02cl3BK.png)

**EAN Attribute**: Select the EAN attribute from your system for xml

![Imgur](https://i.imgur.com/t0Xh3GQ.png)

**Title Attribute**: Select the Title attribute from your system for xml

![Imgur](https://i.imgur.com/0s3njBp.png)

**Description Attribute**: Select the Description attribute from your system for xml

![Imgur](https://i.imgur.com/tntHGI4.png)

**Brand Attribute**: Select the Brand attribute from your system for xml

![Imgur](https://i.imgur.com/HDCKH4y.png)

**Weight Attribute**: Select the Weight attribute from your system for xml

![Imgur](https://i.imgur.com/05OFx3z.png)

**Tracking Voucher Print Format**: Select the printout of the tracking coupon 

![Imgur](https://i.imgur.com/7CaXNfi.png)

### 2.2 Configure Products

Navigate to `Catalog > Products` there are 2 ways to configure your products

**Sell On SHOPFLIX**: Select `Yes` to export it on xml

 **Shipping Lead Time**: Select the lead time that you can have the order ready to be shipped per product
``default value``: Same day

**Offer Date From**: Set Date of offer from for [SHOPFLIX](https://SHOPFLIX.gr)

**Offer Date To**: Set Date of offer to [SHOPFLIX](https://SHOPFLIX.gr)

**Offer Price**: Set the price for offer to [SHOPFLIX](https://SHOPFLIX.gr)

**Offer Qty**: Set the qty of the products for offer to [SHOPFLIX](https://SHOPFLIX.gr)

#### 2.2.1  Edit single product

![Imgur](https://i.imgur.com/9EUhwHh.png)

#### 2.2.2 Edit mulltiple products

![Imgur](https://i.imgur.com/DAjzGhu.png)

### 2.3 SHOPFLIX Order

In this guide we will show how to use the extension

#### 2.3.1 Order Grid

Navigate to `Onecode > SHOPFLIX Order`

![Imgur](https://i.imgur.com/mDhkgeR.png)

##### 2.3.1.1 Order View

![Imgur](https://i.imgur.com/DXXBFst.png)

##### 2.3.1.1.1 Accept Order 

![Imgur](https://i.imgur.com/EBuBfPA.png)

##### 2.3.1.1.2 Reject Order
Enter in rejection form

![Imgur](https://i.imgur.com/pxBNz3X.png)

Select rejection reason

![Imgur](https://i.imgur.com/nX2VXQw.png)

#### 2.3.2 Shipment Grid

Navigate to `Onecode > SHOPFLIX Shipments`

There are 2 ways to print the pdf for courier with tracking data on it.



![Imgur](https://i.imgur.com/tkafZzM.png)

##### 2.3.2.1 Single Print

![Imgur](https://i.imgur.com/sJUVrna.png)

![Imgur](https://i.imgur.com/LRzwdZZ.png)

##### 2.3.2.2 Mass Print

![Imgur](https://i.imgur.com/4wsdGBx.png)

![Imgur](https://i.imgur.com/Y95AoBr.png)

