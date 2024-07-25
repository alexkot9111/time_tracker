import { BrowserRouter, Routes, Route } from 'react-router-dom'
import 'bootstrap/dist/css/bootstrap.min.css'
import './App.css'
import Navigation from './components/main/Navigation'
import MainPage from './components/tracker/MainPage'
import UserList from './components/user/UserList'
import UserCreate from './components/user/UserCreate'
import UserEdit from './components/user/UserEdit'

function App() {

  return (
      <BrowserRouter>
          <Navigation />
          <Routes>
              <Route index element={<MainPage />} />
              <Route path="/users" element={<UserList />} />
              <Route path="/users/create" element={<UserCreate />} />
              <Route path="/users/:userId/edit" element={<UserEdit />} />
          </Routes>
      </BrowserRouter>
  )
}

export default App
