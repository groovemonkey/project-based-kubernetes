# Archive

[12:43] <groovemonkey> Hi all, I'm looking for a way to mount a volume in a container, along with a configfile (from a configmap) at the same mountpoint. It looks like duplicate  mountpoints aren't allowed (I originally thought the mounts would just be applied in order). The error is "Invalid value: "/var/www/html": must be unique"

[12:44] <groovemonkey> the use case is a fairly straightforward wordpress install. I'm mounting a persistent volume for the wp install at /var/www/html, and then trying to add the wp-config.php file via a configmap inside that same directory

[12:45] <groovemonkey> I understand that I could hack around this by mounting the persistent volume at e.g. /var/www and then having the configmap mountpath be /var/www/html, but it just feels really kludgy. Am I missing something?

