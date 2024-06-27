import { BrowserRouter, Routes, Route, Link } from 'react-router-dom'
import 'bootstrap/dist/css/bootstrap.min.css'
import './App.css'
import Navigation from './components/main/Navigation'
import UserList from './components/user/UserList'
import UserCreate from './components/user/UserCreate'
import UserEdit from './components/user/UserEdit'

function App() {

  return (
      <BrowserRouter>
          <Navigation />
          <Routes>
              <Route index element={<UserList />} />
              <Route path="users" element={<UserList />} />
              <Route path="userCreate" element={<UserCreate />} />
              <Route path="user/:id" element={<UserEdit />} />
          </Routes>
      </BrowserRouter>
  )
}

export default App
