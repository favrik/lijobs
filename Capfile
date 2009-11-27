load 'deploy' if respond_to?(:namespace) # cap2 differentiator


set :application, "Jobs"
set :repository,  "git://github.com/favrik/lijobs.git"

set :scm, :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

set :deploy_to, "/home/gdcbeta/favrik"
set :user, "favrik"
set :scm_username, "favrik"
set :use_sudo, false

default_run_options[:pty] = true 

role :web, "beta.gothamdreamcars.net"                          # Your HTTP server, Apache/etc
role :app, "beta.gothamdreamcars.net"
