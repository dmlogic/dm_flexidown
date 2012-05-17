DM Flexidown
============

A Markdown fieldtype for Expression Engine with a few handy options

Installation
------------

Standard EE Fieldtype install

Template usage
--------------

To output data with your default formatting:

    {your_fieldname}

To output data with forced formatting:

    {your_fieldname:format_type}

Where format_type is one of:

* markdown
* html
* br
* raw

Formatting callbacks
--------------------

It's often useful to apply post-processing to content. This can often be the difference between needing a WYSIWYG and not. For example, you could insert a placeholder in the code such as:

    ~~~~

And use this to split the content into two columns with wrapper divs.

Callback are handled by adding a method to the /libraries/flexi_format.php file. Simply add a new method with $data as an argument and be sure to return $data back at the end. You can do anything in between.

Define the callback to run like so:

    {your_fieldname callback="callback_name"}

Multiple callbacks can be run by separating with pipes

    {your_fieldname callback="callback1|callback2"}
    
Roadmap
-------

Entirely up to you. Pull requests welcome

Some things I'd like to do:

* Pre-generate and cache Markdown and HTML content at publish time
* Add Matrix/Low compatibility