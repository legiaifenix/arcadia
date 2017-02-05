# Arcadia

**Created By:** Legiai Fenix
**Started At:** 03/02/2017

**Description:**

Arcadia aims to provide a quick image parsing and uploading solution using laravel tooling. The idea is to just pass the file and have everything done for you
Please note that this package is under development and aims to help the production of developers so they can focus on other aspects
of their projects.
**No harmful effect that comes out of it shall be linked to the package creator**. The use of the package its entirely under
the responsibility of the user and shall not link any responsibility to the creator.

**Introduction to the package name:**
Arcadia was a greek land that became an imaginary country with time by the hand of many poets and artists. Within Arcadia,
reigns happiness, simplicity and beauty.
Since the package aims to provide a simple and clean way to manage the images that are sent by clients we though the name
was just on spot. We aim to provide simple and clean solutions to real problems, leaving the developers happy.

##Functionalities

```
    |       Functionality         |    Function Name   |       Variables       |    Returns       |
    |-----------------------------|--------------------|-----------------------|------------------|
    |  Add Images                 |    uploadImage()   |  file_name            | false, filename  |
    |  List Images From Folder    |    listImages()    |  folder, amount, type | array            |
    |  Deletes Images From Folder |    deleteImage()   |  image with path      | boolean          |
```

##Usage

First we are going to need to create a factory that will manage all our functionalities.

This factory will require you to already provide the path you wish to store your images.
If not provided it will use default as **public** and **uploads**. It is inspired in the Laravel
folder structure where images are sent to the path **<project>/public/<any folder you wish to store the images within>**

If you provide the first arguments it will keep your folder structure to store the images:

```
   new ArcadiaFactory('', 'web', 'uploads');
```

We can also specify a limit for the images upload at the end of the variables when creating the factory object.
If nothing is specified it will limit to 2MB.
In this example we are allowing 50MB. Of course, always make sure your **php.ini** accepts your variables.

```
  new ArcadiaFactory('', 'public', 'uploads', 50000);
```

##It all starts from somewhere

Arcadia will instanciate a model through the factory initiation. You can access such object to ask it to do the functionalities
you desire, by just asking for it via factory:

```
    $arcadiaF->getFactory();
```

##Uploading an image

We can start uploading an image by passing the field parameter through our factory:

```
    $arcadiaF = new ArcadiaFactory('', 'public', 'uploads');
    $arcadiaF->getFactory()->uploadImage('img');
```

Where **img** field exists as **html** file input:

```
    <input type="file" name="img">
```

##List images from a specific folder

Because we wanted to allow you to list any folder you wish, you are able to specify the amount you wish to load
(future development will allow image pagination), from where you wish to list and what type.

Example of listing all svgs from uploads/2017/02:

```
    $factory->getFactory()->listImages('/uploads/2017/02/', -1, 'svg');
```

Example of listing the first 5 pngs from uploads/2017/02:

```
    $factory->getFactory()->listImages('uploads/2017/02/', 5, 'png');
```

Example of returned array:

```
    array(3) {
      [0]=>
      string(80) "/var/www/frame-tester/public/uploads/2017/02/04-02-2017_03-28-42_test_original.jpg"
      [1]=>
      string(80) "/var/www/frame-tester/public/uploads/2017/02/04-02-2017_03-46-13_test_original.jpg"
      [2]=>
      string(80) "/var/www/frame-tester/public/uploads/2017/02/04-02-2017_03-46-18_test_original.jpg"
    }
```

##Deleting images from a folder

Just pass the path for the image with the image name, the program will delete it from there if it sees it exists.
It only allows you to delete images from a folder as it checks if it is an image.
Acceptable types are:

```
    svg
    png
    jpeg
    jpg
    gif
```

You can also delete all images from a folder by using the list function in a for loop with the delete:

```
    $list = $factory->getFactory()->listImages('/uploads/2017/02');
    foreach ($list as $item) {
    	$factory->getFactory()->deleteImage($item);
    }
```

Or even just delete .gifs from a folder:

```
    $list = $factory->getFactory()->listImages('/uploads/2017/02', 'gif');
    foreach ($list as $item) {
    	$factory->getFactory()->deleteImage($item);
    }
```