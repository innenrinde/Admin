# VueAdmin Interface with PHP endpoints
Manage tables content

Test this: https://admin.powerfullapp.ro/
Using credentials: admin[at]test.com / admin


## Use a single configuration to list and to edit an entity
- generate a table list
- create / edit / delete options
```
[{
	"title": "ID",
	"type": "number",
	"field": "id",
	"isPk": true,
	"constraints": {
		"NullForCreation": "No id found"
	}
}, {
	"title": "Category",
	"type": "choice",
	"entity": "Category",
	"field": "category",
	"options": [{
		"value": 3,
		"label": "category 3"
	}, {
		"value": 10,
		"label": "category 10"
	}, {
		"value": 20,
		"label": "category 20"
	}, {
		"value": 21,
		"label": "category 21"
	}, {
		"value": 23,
		"label": "category 23"
	}],
	"constraints": {
		"NotBlank": "Please select a category"
	}
}, {
	"title": "Title",
	"type": "text",
	"field": "title",
	"constraints": {
		"NotBlank": "Please enter an indicator title"
	}
}, {
	"title": "Address",
	"type": "text",
	"field": "address",
	"width": 200
}, {
	"title": "Transaction",
	"type": "text",
	"field": "transaction",
	"width": 150
}, {
	"title": "IP",
	"type": "text",
	"field": "ip",
	"width": 100,
	"constraints": {
		"IpFormat": "Please enter a valid IP address"
	}
}, {
	"title": "Description",
	"type": "text",
	"field": "description",
	"hidden": true
}, {
	"title": "Tags",
	"type": "collection",
	"field": "tags",
	"hidden": true
}]
```




