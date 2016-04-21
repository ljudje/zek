set :application, "static-sek"

set :user, "zek"
server "zek.ljudje.si", :web, :app, :db, primary: true
set :use_sudo, false


set :deploy_to, "/home/#{user}/#{application}"
set :deploy_via, :remote_cache
# set :deploy_via, :copy

set :scm, "git"
set :repository, "git@github.com:ljudje/zek"
set :branch, "zekstatic"

default_run_options[:pty] = true
ssh_options[:forward_agent] = true

after "deploy", "deploy:cleanup" # keep only the last 5 releases

namespace :deploy do
  task :setup_config, roles: :app do
    sudo "ln -nfs #{current_path}/config/nginx.conf /etc/nginx/sites-enabled/#{application}"
  end
end

namespace :deploy do
  task :migrate do
    puts "    not doing migrate because not a Rails application."
  end
  task :finalize_update do
    puts "    not doing finalize_update because not a Rails application."
  end
  task :start do
    puts "    not doing start because not a Rails application."
  end
  task :stop do 
    puts "    not doing stop because not a Rails application."
  end
  task :restart do
    puts "    not doing restart because not a Rails application."
  end
end

