# Hotspotter

A simple Magento widget enabling you to upload an image and assign interactive hotspots.

Simply embed in any page, static block or layout update like so:

```
<p>{{widget type="hotspotter/hotspot"
	image="wysiwyg/windows-desktop.jpg"
	imagesize="1024,768"
	spot1type="product_sku"
	spot1value="SOME-SKU"
	spot1xy="380,450"
	spot2type="cms_block"
	spot2value="SOME-BLOCK-ID"
	spot2xy="70%,535"}}</p>
```

Supports up to 3 hotspots out of the box.
