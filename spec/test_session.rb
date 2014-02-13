# encoding: utf-8

require './spec_helper.rb'

feature 'Session' do
  it 'shows a counter' do
    visit '/sessiontest.php'

    page.should have_content("Object: Instance of class 'A' field1: 1, field2: 2")

    # Do a few other visits
    for i in 2..100
        visit '/sessiontest.php'
        page.should have_content('Counter: ' + i.to_s)
    end

  end
end
