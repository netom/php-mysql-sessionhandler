# encoding: utf-8

require 'capybara/mechanize'
require 'capybara/rspec'
require 'active_record'
require 'pry'
require './models'

ActiveRecord::Base.establish_connection({
  :adapter  => :mysql2,
  :host => "localhost",
  :username => "root",
  :password => '123',
  :database => "mediazone"
})

include Capybara::DSL
Capybara.javascript_driver = Capybara.default_driver = :selenium
Capybara.app_host = 'http://127.0.0.1:3000'
Capybara.run_server = false
