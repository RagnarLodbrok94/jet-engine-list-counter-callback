## Description
Adds a new callback to the Dynamic Field widget to create an ordered list or dynamic table.

## Instructions
- Download, install and activate plugin;
- Go to the Elementor/Blocks editor, Dynamic Field widget and find *Listing’s counter* in the *Callback* option.

## Nested Listings Counter Example
To create a nested counter (e.g. 1, 1.1, 1.2 inside a listing), use two Dynamic Field widgets placed next to each other:

1. First Dynamic Field:
   - Callback: Listing’s counter
   - Query: Current Listing (parent)
   - Parent counter option is true
2. Second Dynamic Field:
   - Callback: Listing’s counter   
   - Query: Current Listing (child)

Example: { Dynamic field => parent counter } . { Dynamic field => child counter }

## Screenshots

![Callback option]( screens/screen-01.jpg "Callback option for Listing" )
>Callback option for Listing

![Callback option]( screens/screen-02.jpg "Custom Query for Listing" )
>Custom Query for Listing

![Output example]( screens/screen-03.jpg "Output example Listing" )
>Output example Listing

![Callback option]( screens/screen-04.jpg "Callback option for Dynamic Table" )
>Callback option for Dynamic Table

![Callback option]( screens/screen-05.jpg "Custom Query for Dynamic Table" )
>Custom Query for Dynamic Table

![Output example]( screens/screen-06.jpg "Output example Dynamic Table" )
>Output example Dynamic Table