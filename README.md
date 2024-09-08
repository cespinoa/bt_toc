# Bootstrap Toolbox Table of Contents (bt_toc)

**Author:** Carlos Espino  
**Drupal.org Profile:** [Carlos Espino](https://www.drupal.org/u/carlos-espino)

## Introduction

The **Bootstrap Toolbox Table of Contents** module allows you to automatically generate a table of contents (TOC) for any node. This TOC is displayed in a block and is built using Bootstrap's ScrollSpy and List Group components for a seamless, responsive experience.

## Features

- Automatically generate a table of contents for nodes based on their headings (h1, h2, h3, h4).
- Configure the TOC at the content type level, with the ability to override settings on individual nodes.
- Integrates with Bootstrap's ScrollSpy for automatic section highlighting as the user scrolls through the content.
- Responsive design with Bootstrap List Group for TOC display.
- "Back to top" link included for easy navigation.

## Installation

1. Download and install the module via Composer:
    ```bash
    composer require drupal/bt_toc
    ```
2. Enable the module:
    ```bash
    drush en bt_toc
    ```

3. Clear the cache:
    ```bash
    drush cr
    ```

## Usage

### Global Configuration (Content Type Level)

1. Navigate to the "Manage Fields" tab of your content type.
2. Enable the "Add Table of Content" option to automatically generate a TOC for all nodes of this type.

### Per-Node Configuration

1. When creating or editing a node, you can override the global TOC setting.
2. The "Override TOC Settings" option allows you to enable or disable the TOC specifically for that node.

### Block Placement

1. Place the **BT TOC** block in a region of your choice via the Block Layout page.
2. The block will automatically generate and display a TOC based on the enabled settings.

## Customization

### Template Overrides

To customize the markup or styling of the TOC block, you can override the default template by copying it to your theme's templates directory:
```/modules/custom/bt_toc/templates/bt-toc-block.html.twig```


### Altering Heading IDs

The module automatically adds sequential IDs to the h1, h2, h3, and h4 elements within the content to ensure proper linking within the TOC. This can be customized or extended by modifying the `bt_toc_add_ids_to_headings()` function.

## Dependencies

- **Bootstrap** (for ScrollSpy and List Group functionality)
- **Bootstrap Toolbox**

## Maintainers

This module is maintained by [Carlos Espino](https://www.drupal.org/u/carlos-espino). Feel free to reach out through my Drupal profile for any support or contributions.

## License

This project is licensed under the GPLv2. See the LICENSE.txt file for more details.

