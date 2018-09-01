## School Report Wordpress Plugin ##

**School-report** is a WopdPress plugin that provides possibilities to create and generate school reports according the requierments of the Russian eductation departments.

This plugin was tested with WordPress 4.8.1-4.8.2.

For its work the plugin uses the EasyUI jQuery framework. 

To have a possibility of the report convertion into the PDF-format it's necessary to have [DK PDF Plugin](https://ru.wordpress.org/plugins/dk-pdf/) installed.

Language of the plugin in Russian. The localization of the plugin is currently impossible.

The plugin is based on the [WordPress-Plugin-Boilerplate plugin template](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).

### Possibilities of the version 1.0.0 ###

- Administrators of the web-site can create and fill lists of education years, grades, classes, students, teachers, and subject;
- The pluging creates two additional roles for its work: the teacher and the head of educational department;
- Teachers can create reports for the different education years, the different parts of the years, and different classes;
- Every report contains information about students absenteeism, academic performance and numbers of lessons for every given subject;
- The report for the study years is created automaticaly and then may be corrected later manualy;
- The head of the education department can check the readyness of the teachers reports; 
- The head of the education department can create an aggregative report automaticaly;
- It's possible to convert created reports into PDF-format using **DK PDF** plugin. 

In current version the plugin provides two short-codes:

**[report-editor]** displays a form for teacher's report creation;

**[report-creator]** displays a form for the head of education department to select a parameters of the aggregative report and generate it.
 